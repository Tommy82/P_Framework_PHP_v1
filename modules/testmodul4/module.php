<?php

namespace modules\testmodul4;

use core\App;
use core\modules\Modules;

class Module extends Modules
{

    /**
     * Erstellen einer neuen Instanz des Testmoduls 4
     * @param App $app
     */
    public function __construct(App &$app)
    {
        parent::__construct($app);
    }

    /**
     * Start des Moduls
     * @return void
     */
    public function start(): void
    {
        try {
            parent::start();

            // Auslesen bzw erkennen der korrekten ID aus der URL
            $id = 0;
            if ( $this->app->urlParameters && count($this->app->urlParameters) > 1 ) {
                $id = intval($this->app->urlParameters[1]);
            }

            // Lese item aus der Tabelle "test"
            $item = $this->app->DB->SelectRow(sprintf("SELECT id, `name`, `detail` FROM `test` WHERE id = %d", $id));
            // Lese AddOns aus der Tabelle "test1"
            $lstItems = $this->app->DB->SelectArr("SELECT id, addOn FROM test1 WHERE test_id = $id");

            // Stelle das gefundene Item für das Template bereit
            $this->app->html->variable_set('ITEM', $item);
            // Stelle eine Liste der AddOns für das Template bereit
            $this->app->html->variable_set("ADDONS", $lstItems);
            // Starte Template
            $this->app->html->output('modules/testmodul4.tpl');
        } catch (\Throwable $e) {
            echo "Fehler: " . $e->getMessage() . " / File: " . $e->getFile() . ": " . $e->getLine() . "<br>";
        }
    }
}