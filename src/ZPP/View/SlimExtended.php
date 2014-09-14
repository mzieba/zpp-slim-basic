<?php

namespace ZPP\View;

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
}