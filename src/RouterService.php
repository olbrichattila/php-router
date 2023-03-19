<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter;

use Aolbrich\PhpRouter\Request\RequestInterface;
use Aolbrich\PhpDiContainer\Container;
use Closure;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;

class RouterService
{
    private const HTTP_METHOD_GET = 'GET';
    private const HTTP_METHOD_POST = 'POST';
    private const HTTP_METHOD_PATCH = 'PATCH';
    private const HTTP_METHOD_PUT = 'PUT';
    private const HTTP_METHOD_DELETE = 'DELETE';

    private const ROUTE_DEFINITION_INDEX = 0;
    private const RESOLVED_ACTION_STRUCT_INDEX = 1;
    private const RESOLVED_ROUTE_CONTROLLER_NAME_INDEX = 0;
    private const RESOLVED_ROUTE_FUNCTION_NAME_INDEX = 1;

    private const VALID_METHODS = [
        self::HTTP_METHOD_GET,
        self::HTTP_METHOD_POST,
        self::HTTP_METHOD_PUT,
        self::HTTP_METHOD_PATCH,
        self::HTTP_METHOD_DELETE,
    ];


    private array $routes = [];
    private array $controllerCache = [];
    private array $middlewareCache = [];

    private array $beforeMiddlewares = [];
    private array $afterMiddlewares = [];

    private array $lastBeforeMiddlewareResults = [];
    private array $lastAfterMiddlewareResults = [];

    public function __construct(
        private readonly RequestInterface $request,
        private readonly Container $container
    ) {
    }

    public function Run(): array
    {
        $method = $this->request->getMethod();
        if (!in_array($method, self::VALID_METHODS)) {
            // @todo create proper exception class
            throw new \Exception('Invalid method ' . $method);
        }

        [$route, $uri] = $this->matchRoutes($method);
        if (!$route) {
            //$todo cannot resolve, @todo throw 404
            die('404');
        }

        $this->lastBeforeMiddlewareResults = $this->runMiddlewares($uri, $method, false);

        $urlParameters = $this->matchingUrlParameters($route[self::ROUTE_DEFINITION_INDEX], $uri);
        $resolvedRouteAction = $route[self::RESOLVED_ACTION_STRUCT_INDEX];
        if (is_callable($resolvedRouteAction)) {
            $this->resolveCallbackRoute($resolvedRouteAction, $urlParameters);
        }

        if (is_array($resolvedRouteAction) && count($resolvedRouteAction) === 2) {
            $this->resolveControllerRoute($resolvedRouteAction, $urlParameters);
        }

        $this->lastAfterMiddlewareResults = $this->runMiddlewares($uri, $method, true);

        return [
            $this->lastBeforeMiddlewareResults,
            $this->lastAfterMiddlewareResults
        ];
    }

    public function get(string $path, Closure|array $route, array $beforeMiddleWares = [], array $afterMiddleWares = []): self
    {
        return $this->method($path, $route, self::HTTP_METHOD_GET, $beforeMiddleWares, $afterMiddleWares);
    }

    public function post(string $path, Closure|array $route, array $beforeMiddleWares = [], array $afterMiddleWares = []): self
    {
        return $this->method($path, $route, self::HTTP_METHOD_POST, $beforeMiddleWares, $afterMiddleWares);
    }

    public function put(string $path, Closure|array $route, array $beforeMiddleWares = [], array $afterMiddleWares = []): self
    {
        return $this->method($path, $route, self::HTTP_METHOD_PUT, $beforeMiddleWares, $afterMiddleWares);
    }

    public function patch(string $path, Closure|array $route, array $beforeMiddleWares = [], array $afterMiddleWares = []): self
    {
        return $this->method($path, $route, self::HTTP_METHOD_PATCH, $beforeMiddleWares, $afterMiddleWares);
    }

    public function delete(string $path, Closure|array $route, array $beforeMiddleWares = [], array $afterMiddleWares = []): self
    {
        return $this->method($path, $route, self::HTTP_METHOD_DELETE, $beforeMiddleWares, $afterMiddleWares);
    }

    public function middleware(array $beforeMiddlewares, array $afterMiddlewares, Closure $callable): self
    {
        $callable(new MiddlewareAdapter($this, $beforeMiddlewares, $afterMiddlewares));

        return $this;
    }

    protected function method(string $path, Closure|array $route, string $method, array $beforeMiddleWares = [], array $afterMiddleWares = []): self
    {
        $this->routes[$method][$path] = $route;
        if (!empty($beforeMiddleWares)) {
            $this->applyMiddlewares($path, $method, $beforeMiddleWares, false);
        }
        if (!empty($afterMiddleWares)) {
            $this->applyMiddlewares($path, $method, $afterMiddleWares, true);
        }

        return $this;
    }

    protected function applyMiddlewares(string $path, string $method, array $middleWares, bool $isAfterMiddleware = false): void
    {
        $middlewareArray = $isAfterMiddleware ? $this->afterMiddlewares : $this->beforeMiddlewares;
        $currentMiddlewares =  $middlewareArray[$method][$path] ?? [];
        $mergedMiddlewares = array_merge($currentMiddlewares, $middleWares);

        if ($isAfterMiddleware) {
            $this->afterMiddlewares[$method][$path] = $mergedMiddlewares;
        } else {
            $this->beforeMiddlewares[$method][$path] = $mergedMiddlewares;
        }
    }

    protected function resolveCallbackRoute(Closure $resolvedRouteAction, array $parameters): Void
    {
        $reflection = new ReflectionFunction($resolvedRouteAction);
        $callParameters = $this->sanitazedUriArguments($parameters, $reflection);

        call_user_func_array($resolvedRouteAction, $callParameters);
    }

    protected function resolveControllerRoute(array $resolvedRouteAction, array $parameters): Void
    {
        $controllerName = $resolvedRouteAction[self::RESOLVED_ROUTE_CONTROLLER_NAME_INDEX];
        $functionName = $resolvedRouteAction[self::RESOLVED_ROUTE_FUNCTION_NAME_INDEX];
        $controller = $this->createControllerOrGetFromCache($controllerName);
        $reflection = new ReflectionMethod($controllerName, $functionName);
        $callParameters = $this->sanitazedUriArguments($parameters, $reflection);

        call_user_func_array([$controller, $functionName], $callParameters);
    }

    protected function matchRoutes(string $method = self::HTTP_METHOD_GET): array
    {
        $uri = $this->cleanedUri();
        if ($uri === null || !isset($this->routes[$method])) {
            return [null, null];
        }

        $matches = array_filter(array_keys($this->routes[self::HTTP_METHOD_GET]), function ($route) use ($uri) {
            $regex = $this->matchingRegexExpression($route);

            return preg_match($regex, $uri);
        });
        $match = reset($matches);

        if (!$match) {
            return [null, $uri];
        }

        return [
            [$match, $this->routes[self::HTTP_METHOD_GET][$match]],
            $uri
        ];
    }

    protected function matchingRegexExpression(string $route): string
    {
        return '/' . implode('\/', array_map(function (string $urlChunk) {
            if (preg_match('/\{.+\}/', $urlChunk)) {
                return '(.*)';
            }

            return $urlChunk;
        }, explode('/', $route))) . '$/';
    }

    protected function matchingUrlParameters(string $route, string $uri): array
    {
        $uriParts = explode('/', $uri);
        $parameters = [];

        foreach (explode('/', $route) as $index => $urlChunk) {
            if (preg_match('/\{.+\}/', $urlChunk)) {
                $parameters[
                    str_replace(['{', '}'], '', $urlChunk)
                    ] = $uriParts[$index];
            }
        }

        return $parameters;
    }

    protected function createControllerOrGetFromCache(string $controllerName): object
    {
        if ($controller = $this->getController($controllerName)) {
            return $controller;
        }

        $controller = $this->container->get($controllerName);
        $this->controllerCache[$controllerName] = $controller;

        return $controller;
    }

    public function getController(string $controllerName): ?object
    {
        if (isset($this->controllerCache[$controllerName])) {
            return $this->controllerCache[$controllerName];
        }

        return null;
    }

    public function getMiddlewareInstances(): array
    {
        return $this->middlewareCache;
    }

    protected function sanitazedUriArguments(
        array $parameters,
        ReflectionFunction|ReflectionMethod $reflection
    ): array {
        $arguments  = $reflection->getParameters();
        $callParameters = [];
        foreach ($arguments as $argument) {
            $name = $argument->getName();
            $callParameter = $parameters[$name] ?? null;
            if ($callParameter !== null) {
                $type = $argument->getType();
                if ($type && $type instanceof ReflectionNamedType) {
                    $callParameter = $this->converArgument($type->getName(), $callParameter);
                }

                $callParameters[] = filter_var($callParameter, FILTER_SANITIZE_STRING);
            } else {
                $type = $argument->getType();
                if ($type) {
                    $callParameter = $this->container->get($type->getName());
                    $callParameters[] = $callParameter;
                } else {
                    $callParameters[] = null;
                }
            }
        }

        return $callParameters;
    }

    protected function converArgument(string $type, string $argument): mixed
    {
        $result = filter_var($argument, FILTER_SANITIZE_STRING);
        switch ($type) {
            case 'int':
                $result = intval($result);
                break;
            case 'float':
                $result = floatval($result);
        }

        return $result;
    }

    protected function cleanedUri(): ?string
    {
        $uri = explode('?', $this->request->getUri());
        $uri = reset($uri);

        if (!$uri) {
            return null;
        }

        return $uri === '/' ? $uri : rtrim($uri, '/');
    }

    protected function runMiddlewares(string $uri, string $method, bool $isAfterMiddleware = false): array
    {
        $results = [];
        $middlewares = $isAfterMiddleware ? $this->afterMiddlewares : $this->beforeMiddlewares;
        if (isset($middlewares[$method][$uri])) {
            foreach ($middlewares[$method][$uri] as $middleware) {
                if (!isset($this->middlewareCache[$middleware])) {
                    $middlewareInstance = $this->container->resolve($middleware);
                    $this->middlewareCache[$middleware] = $middlewareInstance;
                }

                $results[$method][$uri][$middleware] = $this->middlewareCache[$middleware]->handle();
            }
        }

        return $results;
    }
}
