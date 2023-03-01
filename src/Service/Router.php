<?php

declare(strict_types=1);

namespace App\Service;

use ReflectionClass;

class Router
{
    private array $routes = [
        '/users/upload' => [
            'method' => 'POST',
            'controller' => 'App\Controller\UserController',
            'function' => 'usersUpload'
        ],
        '/users/send' => [
            'method' => 'POST',
            'controller' => 'App\Controller\UserController',
            'function' => 'usersSend'
        ],
        '/database/migrations/migrate' => [
            'method' => 'GET',
            'controller' => 'App\Controller\MigrationController',
            'function' => 'migration'
        ],
    ];

    public function __construct(
        private string $requestUri,
        private string $requestMethod
    ) {
    }

    /**
     * @throws \ReflectionException
     */
    public function execute(): void
    {
        $routeFound = false;
        foreach ($this->routes as $route => $config) {
            if ($this->requestUri === $route && $this->requestMethod === $config['method']) {
                $controllerName = $config['controller'];
                $functionName = $config['function'];

                $class = $this->newInstanceOf($controllerName);
                $response = $class->$functionName();

                print_r(json_encode($response));

                $routeFound = true;
                break;
            }
        }

        if (!$routeFound) {
            $this->addNotFoundHeader();
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function newInstanceOf(string $class)
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return new $class;
        }

        $params = $constructor->getParameters();
        if (count($params) === 0) {
            return new $class;
        }

        $newInstanceParams = [];
        foreach ($params as $param) {
            if ($param->getClass() === null) {
                continue;
            }

            $className = $param->getClass()->getName();
            $newInstanceParams[] = $this->newInstanceOf($className);
        }

        return $reflection->newInstanceArgs($newInstanceParams);
    }

    private function addNotFoundHeader(): void
    {
        header('HTTP/1.0 404 Not Found');
    }
}
