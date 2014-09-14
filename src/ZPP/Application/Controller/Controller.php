<?php

namespace ZPP\Application\Controller;

class Controller
{
    /**
     * @var \Slim\Slim
     */
    protected $app = null;

    /**
     * 
     * @return \Slim\Slim
     */
    protected function getApp() {
        if (null === $this->app) {
            $this->app = \Slim\Slim::getInstance();
        }
        return $this->app;
    }
}