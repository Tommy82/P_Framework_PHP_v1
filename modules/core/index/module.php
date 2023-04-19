<?php

/**
 * Allgemeine Startseite und Backup falls kein Modul angegeben wurde
 */

namespace modules\core\index;

use core\App;
use core\modules\Modules;

class Module extends Modules {
    /**
     * Erstellen einer neuen Instanz des Index Moduls
     * @param App $app Globale App
     */
    public function __construct(App &$app)
    {
        // Starten des Konstruktors der abstrakten Klasse in "core/modules"
        parent::__construct($app);
    }

    /**
     * Handlungen innerhalb des Moduls ausführen
     * @return void
     */    public function start(): void
    {
        // Setzen des Templates
        $this->app->html->template_set("core/index.tpl");
        // Ausgabe des Templates
        $this->app->html->output();
    }
}