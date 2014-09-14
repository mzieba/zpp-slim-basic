<?php

namespace ZPP;

class ComposerEvents
{
    public static function postInstall(\Composer\Script\CommandEvent $event) {
        $ds = DIRECTORY_SEPARATOR;
        $ext = '';

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $ext = '.bat';
        }
        
        print `vendor{$ds}bin{$ds}bowerphp{$ext} install`;
    }

    public static function postUpdate(\Composer\Script\CommandEvent $event) {
        $ds = DIRECTORY_SEPARATOR;
        $ext = '';

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $ext = '.bat';
        }
        
        print `vendor{$ds}bin{$ds}bowerphp{$ext} update`;
    }
}