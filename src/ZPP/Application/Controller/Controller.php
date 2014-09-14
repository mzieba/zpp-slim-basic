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
    
    /**
     * 
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render($template, array $data = null, $status = null) {
        $app = $this->getApp();
        
        if (null !== $data) {
            $app->view()->appendData($data);
        }
        
        if (null !== $status) {
            $app->response()->status($status);
        }

        $content = $app->view()->fetch($template);
        
        if ($app->view()->extend()) {
            $app->view()->appendData([
                'content' => $content
            ]);
            $content = $app->view()->fetch($app->view()->extend());
        }

        echo $content;
    }
}