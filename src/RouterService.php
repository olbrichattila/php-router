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
    private const ROUTE_DEFINITION_INDEX = 0;
    private const RESOLVED_ACTION_STRUCT_INDEX = 1;
    private const RESOLVED_ROUTE_CONTROLLER_NAME_INDEX = 0;
    private const RESOLVED_ROUTE_FUNCTION_NAME_INDEX = 1;

    private const VALID_METHODS = [
        self::HTTP_METHOD_GET,
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
        foreach (self::VALID_METHODS as $method) {
            [$route, $uri] = $this->matchRoutes($method);
            if (!$route) {
                //$todo cannot resolve, @todo throw 404
                die('404');
                // return;
            }

            $this->lastBeforeMiddlewareResults = $this->runMiddlewares($uri, $method, false);

            $parameters = $this->getMatches($route[self::ROUTE_DEFINITION_INDEX], $uri);
            $resolvedRouteAction = $route[self::RESOLVED_ACTION_STRUCT_INDEX];

            // $middlewareResults = $this->lastBeforeMiddlewareResults[$method][$uri];

            if (is_callable($resolvedRouteAction)) {
                $this->resolveCallbackRoute($resolvedRouteAction, $parameters);
            }

            if (is_array($resolvedRouteAction) && count($resolvedRouteAction) === 2) {
                $this->resolveControllerRoute($resolvedRouteAction, $parameters);
            }

            $this->lastAfterMiddlewareResults = $this->runMiddlewares($uri, $method, true);
        }

        return [
            $this->lastBeforeMiddlewareResults,
            $this->lastAfterMiddlewareResults
        ];
    }

    public function get(string $path, Closure|array $route, array $beforeMiddleWares = [], array $afterMiddleWares = []): self
    {
        $this->routes[self::HTTP_METHOD_GET][$path] = $route;
        if (!empty($beforeMiddleWares)) {
            $this->applyMiddlewares($path, self::HTTP_METHOD_GET, $beforeMiddleWares, false);
        }
        if (!empty($afterMiddleWares)) {
            $this->applyMiddlewares($path, self::HTTP_METHOD_GET, $afterMiddleWares, true);
        }

        return $this;
    }

    public function middleware(array $beforeMiddlewares, array $afterMiddlewares, Closure $callable): self
    {
        $callable(new MiddlewareAdapter($this, $beforeMiddlewares, $afterMiddlewares));

        return $this;
    }

    private function applyMiddlewares(string $path, string $method, array $middleWares, bool $isAfterMiddleware = false): void
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

    private function resolveCallbackRoute(Closure $resolvedRouteAction, array $parameters): Void
    {
        $reflection = new ReflectionFunction($resolvedRouteAction);
        $callParameters = $this->getArguments($parameters, $reflection);

        call_user_func_array($resolvedRouteAction, $callParameters);
    }

    private function resolveControllerRoute(array $resolvedRouteAction, array $parameters): Void
    {
        $controllerName = $resolvedRouteAction[self::RESOLVED_ROUTE_CONTROLLER_NAME_INDEX];
        $functionName = $resolvedRouteAction[self::RESOLVED_ROUTE_FUNCTION_NAME_INDEX];
        $controller = $this->createControllerOrGetFromCache($controllerName);
        $reflection = new ReflectionMethod($controllerName, $functionName);
        $callParameters = $this->getArguments($parameters, $reflection);

        call_user_func_array([$controller, $functionName], $callParameters);
    }

    private function matchRoutes(string $method = self::HTTP_METHOD_GET): array
    {
        $uri = $this->cleanedUri();
        if ($uri === null || !isset($this->routes[$method])) {
            return [null, null];
        }

        $matches = array_filter(array_keys($this->routes[self::HTTP_METHOD_GET]), function ($route) use ($uri) {
            $regex = $this->getMathRegex($route);

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

    private function getMathRegex(string $route): string
    {
        return '/' . implode('\/', array_map(function (string $urlChunk) {
            if (preg_match('/\{.+\}/', $urlChunk)) {
                return '(.*)';
            }

            return $urlChunk;
        }, explode('/', $route))) . '$/';
    }

    private function getMatches(string $route, string $uri): array
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

    private function createControllerOrGetFromCache(string $controllerName): object
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

    private function getArguments(
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

    private function converArgument(string $type, string $argument): mixed
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

    private function cleanedUri(): ?string
    {
        $uri = explode('?', $this->request->getUri());
        $uri = reset($uri);

        if (!$uri) {
            return null;
        }

        return $uri === '/' ? $uri : rtrim($uri, '/');
    }

    private function runMiddlewares(string $uri, string $method, bool $isAfterMiddleware = false): array
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
