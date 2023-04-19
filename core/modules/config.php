<?php

/**
 * Laden der Konfigurationsdatei
 */

namespace core\modules;

class Config {
    /** @var array|mixed $database Konfiguration für Datenbank */
    public array $database;
    /** @var array|mixed $smarty Konfiguration für Smarty Template Engine */
    public array $smarty;

    /**
     * Erstellen einer neuen Instanz für die Konfiguration
     */
    public function __construct() {
        // Laden der Ini Datei und schreiben in ein Array
        $iniArray = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . '/config/config.ini', true);

        // Laden der Datenbankdaten in lokale Variable $database
        if ($iniArray && count($iniArray) >0 && $iniArray["Database"] && count($iniArray["Database"]) > 0 ) {
            $this->database = $iniArray["Database"];
        }

        // Laden der Smarty Daten in lokale Variable $smarty
        if ( $iniArray && count($iniArray) > 0 && $iniArray["Smarty"] && count($iniArray["Smarty"]) >0 ) {
            $this->smarty = $iniArray["Smarty"];
        }
    }
}