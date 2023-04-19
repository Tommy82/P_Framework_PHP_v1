<?php

namespace core\modules;

use core\App;

abstract class Modules
{
    /**
     * @var App $app Globale App
     */
    protected App $app;

    // Protected damit diese nur in einer vererbten Klasse aufrufbar ist
    /**
     * Konstruktor der abstrakten Klasse
     * @param App $app Globale App
     */
    protected function __construct(App &$app) {
        // Setzen der globalen App
        $this->app = &$app;
    }

    // Protected damit diese nur in einer vererbten Klasse aufrufbar ist
    /**
     * Allgemeine Handlungen für alle Module
     * @return void
     */
    protected function start() {
    }
}