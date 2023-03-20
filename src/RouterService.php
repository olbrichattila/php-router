<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter;

use Aolbrich\PhpRouter\Http\Request\RequestInterface;
use Aolbrich\PhpRouter\Http\Response\ResponseInterface;
use Aolbrich\PhpRouter\Http\Response\Response;
use Aolbrich\PhpRouter\Http\Response\JsonResponse;
use Aolbrich\PhpRouter\Http\Request\Request;
use Aolbrich\PhpDiContainer\Container;
use Closure;
use ReflectionFunction;
use ReflectionMethod;

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
    private array $middlewareCache = [];
    private array $beforeMiddlewares = [];
    private array $afterMiddlewares = [];
    private mixed $lastMiddlewareResult = null;
    private mixed $lastControllerResult = null;
    private RequestInterface $request;

    public function __construct(
        private readonly Container $container
    ) {
        if (!$container->has(Request::class)) {
            $container->set(Request::class, function ($container) {
                return $container->singleton(Request::class);
            });
        }
        $container->set(Response::class, function ($container) {
            return $container->singleton(Response::class);
        });

        $container->set(JsonResponse::class, function ($container) {
            return $container->singleton(JsonResponse::class);
        });
        
        $this->request = $container->get(Request::class);
    }

    /**
     * Summary of Run
     * @throws \Exception
     * @return ResponseInterface
     */
    public function Run(): ResponseInterface
    {
        $method = $this->request->getMethod();
        if (!in_array($method, self::VALID_METHODS)) {
            // @todo create proper exception class
            throw new \Exception('Invalid method ' . $method);
        }

        [$route, $uri] = $this->matchRoutes($method);
        if (!$route) {
            //@todo do it with proper DI
            $response = new Response();
            $response->setResponseCode(404);
            $response->setBody('404 Page not found');

            return $response;
        }

        $this->lastMiddlewareResult = null;
        $this->runMiddlewares($uri, $method, false);

        $urlParameters = $this->matchingUrlParameters($route[self::ROUTE_DEFINITION_INDEX], $uri);
        $resolvedRouteAction = $route[self::RESOLVED_ACTION_STRUCT_INDEX];
        if (is_callable($resolvedRouteAction)) {
            $this->lastControllerResult = $this->resolveCallbackRoute($resolvedRouteAction, $urlParameters);
        }

        if (is_array($resolvedRouteAction) && count($resolvedRouteAction) === 2) {
            $this->lastControllerResult = $this->resolveControllerRoute($resolvedRouteAction, $urlParameters);
        }

        $this->runMiddlewares($uri, $method, true);

        if ($this->lastControllerResult instanceof ResponseInterface) {
            return $this->lastControllerResult;    
        }

         $response = new Response();
         $response->setBody((string)$this->lastControllerResult);
        
         return $response;
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

    protected function resolveCallbackRoute(Closure $resolvedRouteAction, array $parameters): mixed
    {
        $reflection = new ReflectionFunction($resolvedRouteAction);
        $callParameters = $this->sanitazedUriArguments($parameters, $reflection);

        return call_user_func_array($resolvedRouteAction, $callParameters);
    }

    protected function resolveControllerRoute(array $resolvedRouteAction, array $parameters): mixed
    {
        $controllerName = $resolvedRouteAction[self::RESOLVED_ROUTE_CONTROLLER_NAME_INDEX];
        $functionName = $resolvedRouteAction[self::RESOLVED_ROUTE_FUNCTION_NAME_INDEX];
        $controller = $this->getController($controllerName);
        $reflection = new ReflectionMethod($controllerName, $functionName);
        $callParameters = $this->sanitazedUriArguments($parameters, $reflection);

        return call_user_func_array([$controller, $functionName], $callParameters);
    }

    protected function matchRoutes(string $method = self::HTTP_METHOD_GET): array
    {
        $uri = $this->cleanedUri();
        if ($uri === null || !isset($this->routes[$method])) {
            return [null, null];
        }

        $matches = array_filter(array_keys($this->routes[$method]), function ($route) use ($uri) {
            $regex = $this->matchingRegexExpression($route);

            return preg_match($regex, $uri);
        });
        $match = reset($matches);

        if (!$match) {
            return [null, $uri];
        }

        return [
            [$match, $this->routes[$method][$match]],
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

    public function getController(string $controllerName): ?object
    {
        return $this->container->singleton($controllerName);
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
        $dependencies = $this->container->getDependencies(
            $arguments,
            '',
            $parameters
        );

        foreach (array_keys($dependencies) as $key) {
            if (is_string($dependencies[$key])) {
                $dependencies[$key] = filter_var($dependencies[$key], FILTER_SANITIZE_ADD_SLASHES);
            }
        }

        return $dependencies;
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
        // @TODO middleware should implement intetface, and shoud be checked if it is the isntance of
        $results = [];
        $middlewares = $isAfterMiddleware ? $this->afterMiddlewares : $this->beforeMiddlewares;
        if (isset($middlewares[$method][$uri])) {
            foreach ($middlewares[$method][$uri] as $middleware) {
                if (!isset($this->middlewareCache[$middleware])) {
                    $middlewareInstance = $this->container->get($middleware);
                    $this->middlewareCache[$middleware] = $middlewareInstance;
                }

                $reflection = new ReflectionMethod($this->middlewareCache[$middleware], 'handle');
                $dependencies = $this->container->getDependencies(
                    $reflection->getParameters(),
                    '',
                    []
                );

                $this->lastMiddlewareResult = call_user_func_array([$this->middlewareCache[$middleware], 'handle'], $dependencies);
                $results[$method][$uri][$middleware] = $this->lastMiddlewareResult;
            }
        }

        return $results;
    }
}
