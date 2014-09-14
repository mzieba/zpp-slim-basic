<?php

namespace ZPP\Application\Router;

class Routes
{
    /**
     * route
     * @var Route[]
     */
    protected $routes;

    /**
     */
    public function clearRoutes() {
        $this->routes = [];
    }

    /**
     * 
     * @param array $routes
     * @return Routes
     */
    public function fromArray($routes) {
        $this->clearRoutes();

        foreach ($routes as $name => $routeData) {
            $routeData['name'] = $name;

            $route = new Route;
            $route->fromArray($routeData);

            if ($route->isValid()) {
                $this->routes[$route->name()] = $route;
            }
        }
    }

    /**
     * 
     * @return Routes
     */
    public function install(\Slim\Slim $app) {
        foreach ($this->routes as $route) {
            
            $slimRoute = $app
                ->map($route->route(), function() use ($route) {
                    // parametry akcji/metody
                    $params = array_merge($route->defaults(), func_get_args());

                    // kontroler
                    $controller = false !== strpos('\\', $route->controller())
                            ? $route->controller()
                            : '\ZPP\Application\Controller\\' . $route->controller();
                    
                    // jeśli istnieje
                    if (class_exists($controller)) {
                        $controllerObject = new $controller;
                        return call_user_func_array([$controllerObject, $route->action()], $params);
                    } else {
                        throw new \Exception('Cannot find controller ' . $controller);
                    }
                })
                ->name($route->name());
                
            call_user_func_array([$slimRoute, 'via'], $route->methods());
        }
    }
}