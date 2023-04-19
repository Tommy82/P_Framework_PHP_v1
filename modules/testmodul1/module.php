<?php

namespace modules\testmodul1;

use core\App;
use core\modules\Modules;

class Module extends Modules {

    /**
     * Erstellen einer neuen Instanz des Testmoduls1
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
     */
    public function start(): void
    {
        // Starte der globalen Handlungen in der abstrakten Klasse "core/modules".
        // Hier können Handlungen, welche immer und in ALLEN Modulen ausgeführt werden sollen eingefügt werden.
        parent::start();

        // Auslesen der XML-Datei und schreiben in ein Array
        $data = $this->app->converter::XMLFileToArray($_SERVER["DOCUMENT_ROOT"] . "/testdata/test_xml.xml");

        // Übergabe des Arrays an das Template
        $this->app->html->variable_set("ELEMENTS", $data);
        // Setzen des Templates
        $this->app->html->template_set("modules/testmodul1.tpl");
        // Aufrufen des Templates
        $this->app->html->output();
    }
}