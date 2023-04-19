<?php

/**
 * Autoload Function damit die Dateien nicht einzeln eingebunden werden müssen.
 * Wichtig! Zur korrekten Funktion MUSS die Ordnerstruktur eingehalten werden!
 */

class Autoloader
{
    public function __construct()
    {
        spl_autoload_register(array($this, 'load_class'));
    }

    public static function register(): void
    {
        new Autoloader();
    }

    public function load_class($class_name): void
    {
        $file = strtolower(str_replace('\\','/',$class_name)).'.php';
        if(file_exists($file))
        {
            require_once($file);
        }
    }
  }
  
  Autoloader::register();