<?php

namespace ZPP\Application\Controller;

class Index extends \ZPP\Application\Controller\Controller
{
    /**
     * obsługa /
     */
    public function index() {
        $this->render('index/index.phtml', [
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }

    /**
     * obsługa /hello/:name
     * @param string $name
     */
    public function hello($name) {
        $this->render('index/index.phtml', [
            'name' => $name,
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }
}