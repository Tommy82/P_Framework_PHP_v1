<?php

namespace modules\testmodul3;

use core\App;
use core\modules\Modules;

class Module extends Modules
{

    /**
     * Erstellen einer neuen Instanz des Testmoduls3
     * @param App $app
     */
    public function __construct(App &$app)
    {
        parent::__construct($app);
    }

    /**
     * Handlungen innerhalb des Moduls ausführen
     * @return void
     */
    public function start(): void
    {
        try {
            parent::start();

            // Laden aller Items aus der Datenbank
            $lstItems = $this->app->DB->SelectArr("SELECT id, `name`, `detail` FROM `test` WHERE id > 0 ");

            // Bereitstellen aller aus der DB geladenen Items für das Template
            $this->app->html->variable_set('ITEMS', $lstItems);
            // Ausgabe des Templates
            $this->app->html->output('modules/testmodul3.tpl');
        } catch ( \Throwable $e ) {
            echo "Fehler: " . $e->getMessage() . " / File: " . $e->getFile() . ": " . $e->getLine() . "<br>";
        }
    }

    /**
     * Installation der Datenbank und der Testdaten
     * @return void
     */
    public function install(): void
    {
        echo "> Installiere MySQL Tabellen<br>";

        // Installation der Tabelle - Test
        $tableName = 'test';
        $tblExists = $this->app->DB->CheckTable($tableName);
        if ( $tblExists ) {
            $this->app->DB->CheckColumn($tableName, 'id', 'int(11)', 'UNSIGNED AUTO_INCREMENT PRIMARY KEY');
            $this->app->DB->CheckColumn($tableName, 'name', 'varchar(50)');
            $this->app->DB->CheckColumn($tableName, 'detail', 'varchar(50)');
        }

        // Installation der Tabelle - Test1 mit Abhängigkeit zu Test
        $tableName2 = 'test1';
        $tblExists2 = $this->app->DB->CheckTable($tableName2);
        if ( $tblExists2 ) {
            $this->app->DB->CheckColumn($tableName2, 'id', 'int(11)', 'UNSIGNED AUTO_INCREMENT PRIMARY KEY');
            $this->app->DB->CheckColumn($tableName2, 'test_id', 'int(11)', 'UNSIGNED NOT NULL', $tableName, 'id');
            $this->app->DB->CheckColumn($tableName2, 'addOn', 'varchar(50)');
        }

        // Installation von Testdaten
        echo "> Installiere Testdaten<br>";
        $this->install_item('wasser', 'still', ['keine kohlensaeure', 'klar', 'schmeckt nicht']);
        $this->install_item('sprudelwasser', 'blubbert', ['kohlensaere', 'klar', 'schmeckt']);

        echo "> Fertig<br>";
    }

    /**
     * Hinzufügen von Testdaten
     * @param string $name
     * @param string $detail
     * @param array $addOn
     * @return void
     */
    function install_item(string $name, string $detail, array $addOn): void
    {
        try {

            // Prüfe ob Name schon in Datenbank existiert
            $found = $this->app->DB->SelectRow("SELECT id FROM test WHERE `name` = '$name'");
            if ( !$found ) {
                // Falls noch nicht exisiert, schreibe die Daten in "test" und lese die neue "id" aus
                $test_id = $this->app->DB->Insert("INSERT INTO test ( `name`, `detail` ) VALUES ( '$name', '$detail')");
                // Falls erfolgreich gespeichert (neue ID ist größer 0) ...
                if ( $test_id && intval($test_id) >0 ) {
                    // ... prüfe ob AddOns vorhanden sind ...
                    if ( $addOn && count($addOn) >0 ) {
                        // ... solange AddOns vorhanden sind ...
                        foreach ( $addOn as $item ) {
                            // ... Prüfe ob AddOn noch nicht in DB ist ...
                            $foundAddOn = $this->app->DB->SelectRow("SELECT id FROM test1 WHERE `test_id` = $test_id AND `addOn` = '$item'");
                            if ( !$foundAddOn ) {
                                // ... ... speichere in DB falls noch nicht vorhanden
                                $this->app->DB->Insert("INSERT INTO test1 (`test_id`, `addOn`) VALUES ( $test_id, '$item')");
                            }
                        }
                    }
                }
            }

        } catch ( \Throwable $e ) {
            echo "Fehler beim Hinzufügen der Daten: " . $e->getMessage() . "<br>";
        }
    }
}