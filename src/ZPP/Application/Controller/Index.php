<?php

namespace ZPP\Application\Controller;

class Index extends \ZPP\Application\Controller\Controller
{
    public function index() {
        $app = $this->getApp();

        $pageContent = $app->view()->fetch('index/index.phtml', [
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    
        $app->render('common/layout.phtml', [
            'content' => $pageContent
        ]);
    }

    public function hello($name) {
        $app = $this->getApp();

        $pageContent = $app->view()->fetch('index/index.phtml', [
            'name' => $name,
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    
        $app->render('common/layout.phtml', [
            'content' => $pageContent
        ]);
    }
}