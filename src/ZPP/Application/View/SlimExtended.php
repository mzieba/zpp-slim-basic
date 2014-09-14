<?php

namespace ZPP\Application\View;

class SlimExtended extends \Slim\View
{
    /**
     * rozszerzamy widok o szablon
     * @var string
     */
    protected $templateToExtend = null;


    /**
     * ustawia/pobiera szablon do rozszerzenia
     * @param string $template
     * @return string|null
     */
    public function extend($template = null) {
        if (null !== $template) {
            $this->templateToExtend = $template;
        }
        
        return $this->templateToExtend;
    }


    /**
     * klucz do rozszerzenia
     * @return string
     */
    public function extendKey() {
        return 'content';
    }
    
    
    /**
     * @return \Slim\Slim
     */
    public function app() {
        return \Slim\Slim::getInstance();
    }
}