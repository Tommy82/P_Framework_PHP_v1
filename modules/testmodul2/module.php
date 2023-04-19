<?php

namespace modules\testmodul2;

use Core\App;
use core\modules\Modules;

class Module extends Modules {

    /**
     * Erstellen einer neuen Instanz des Testmoduls2
     * @param App $app Globale App
     */
    public function __construct(App &$app) {
        // Starten des Konstruktors der abstrakten Klasse in "core/modules"
        parent::__construct($app);
    }

    /**
     * Handlungen innerhalb des Moduls ausführen
     * @return void
     */
    public function start(): void {
        /** Interne ID */
        $id = 0;
        /** gefundener Datensatz */
        $current = [];

        // Starte der globalen Handlungen in der abstrakten Klasse "core/modules".
        // Hier können Handlungen, welche immer und in ALLEN Modulen ausgeführt werden sollen eingefügt werden.
        parent::start();

        // Auslesen der XML-Datei und schreiben in ein Array
        $data = $this->app->converter::XMLFileToArray($_SERVER["DOCUMENT_ROOT"] . "/testdata/test_xml.xml");

        // Auslesen bzw erkennen der korrekten ID aus der URL
        if ( $this->app->urlParameters && count($this->app->urlParameters) > 1 ) {
            $id = intval($this->app->urlParameters[1]);
        }

        // Durchsuchen aller aus der XML geladenen Elemente um den korrekten Datensatz zu finden
        for ( $i = 0; $i < count($data["element"]); $i++ ) {
            if ( intval($data["element"][$i]["id"]) == $id ) {
                $current = $data["element"][$i];
            }
        }

        // Übergebe das Array an das Template
        $this->app->html->variable_set('CURRENT', $current);
        // Setzen des Templates
        $this->app->html->template_set("modules/testmodul2.tpl");
        // Aufrufen des Templates
        $this->app->html->output();

    }
}