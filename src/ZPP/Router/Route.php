<?php

namespace ZPP\Router;

class Route
{
    /**
     * name
     * @var string
     */
    protected $name;
    
    /**
     * route
     * @var string
     */
    protected $route;
    
    /**
     * available methods
     * @var array
     */
    protected $methods;
    
    /**
     * route's controller
     * @var string
     */
    protected $controller;
    
    /**
     * route's action
     * @var string
     */
    protected $action;
    
    /**
     * route param's default values
     * @var array
     */
    protected $defaults;
    
    /**
     * route param's conditions
     * @var array
     */
    protected $conditions;
    
    
    /**
     * 
     * @param string $route
     * @return string
     */
    public function route($route = null) {
        if (null !== $route) {
            $this->route = $route;
        }
        return $this->route;
    }
    
    /**
     * 
     * @param string $name
     * @return string
     */
    public function name($name = null) {
        if (null !== $name) {
            $this->name = $name;
        }
        return $this->name;
    }
    
    
    /**
     * 
     * @param string|array $methods
     * @return string
     */
    public function methods($methods = null) {
        if (null !== $methods) {
            if (is_string($methods)) {
                $methods = preg_split('/[^a-z]+/', trim($methods));
            }
            
            array_walk($methods, function(&$method) {
                $method = strtoupper($method);
            });
            
            $availableMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTION'];

            $methods = array_filter($methods, function($method) use ($availableMethods) {
                return in_array($method, $availableMethods);
            });
            
            $this->methods = $methods;
        }
        return $this->methods;
    }
    
    /**
     * 
     * @param string $controller
     * @return string
     */
    public function controller($controller = null) {
        if (null !== $controller) {
            $this->controller = $controller;
        }
        return $this->controller;
    }
    
    /**
     * 
     * @param string $action
     * @return string
     */
    public function action($action = null) {
        if (null !== $action) {
            $this->action = $action;
        }
        return $this->action;
    }
    
    /**
     * 
     * @param string $defaults
     * @return string
     */
    public function defaults($defaults = null) {
        if (null !== $defaults) {
            $this->defaults = $defaults;
        }
        return $this->defaults;
    }
    
    /**
     * 
     * @param string $conditions
     * @return string
     */
    public function conditions($conditions = null) {
        if (null !== $conditions) {
            $this->conditions = $conditions;
        }
        return $this->conditions;
    }
    
    /**
     * 
     * @param array $routeData
     * @return Route
     */
    public function fromArray($routeData) {
        $routeData = array_merge([
            'methods' => ['get'],
            'defaults' => []
        ], $routeData);
        
        foreach ($routeData as $param => $value) {
            if (method_exists($this, $param)) {
                call_user_func_array([$this, $param], [$value]);
            }
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function isValid() {
        return strlen($this->name) && strlen($this->route) && strlen($this->controller) && strlen($this->action);
    }
}