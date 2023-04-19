<?php

namespace core;

use core\modules\Converter;
use core\modules\Modules;
use core\modules\Output_Smarty;
use core\modules\Database as CoreDatabase;
use core\modules\Config as CoreConfig;

class App
{
    /** @var string $currentModule Aktuelles erkanntes Modul */
    public string $currentModule;
    /** @var array $urlParameters Alle Parameter aus der URL */
    public array $urlParameters;
    /** @var bool $isBackend Gibt an ob das aufgerufene Modul ein Backend Modul ist */
    public bool $isBackend;
    /** @var CoreDatabase $DB Datenbankanbindung */
    public CoreDatabase $DB;

    /** @var CoreConfig $config Konfigurationsdatei */
    public CoreConfig $config;

    /** @var Output_Smarty $html Ausgabe für Templates (Smarty) */
    public Output_Smarty $html;

    /** @var Modules $module Modulberechnungen zum erkennen des korrekten Moduls */
    public Modules $module;

    /** @var Converter $converter Allgemeine Converter */
    public Converter $converter;

    /**
     * Neue Instanz der App
     */
    public function __construct()
    {
        $this->config = new CoreConfig();                   // Initialisieren und Laden der Konfiguration
        $this->converter = new Converter();                 // Initialisieren der Converter
        $this->DB = new CoreDatabase($this->config);        // Initialisieren und Verbindungen zur Datenbank
        $this->html = new Output_Smarty($this->config);     // Initialisieren der HTML Ausgabe

        $this->url_handle();                                 // Verarbeiten der aktuellen URL (SEO Optimized)

        // Laden des aktuellen Moduls
        if ( !$this->module_load() ) {
            // Falls Modul nicht gefunden, Backup auf "index"
            $this->currentModule = "index";
            // Lade Index Modul
            $this->module_load();
        }
    }

    /**
     * Behandlung der SEO Optimized URL
     * @return void
     */
    private function url_handle(): void
    {
        $slug = array_key_exists('slug', $_GET) ? $_GET['slug'] : null;                                // "slug" ist in der .htaccess angegeben als Parameter
        if ( $slug ) {                                                                                      // Wenn Parameter vorhanden sind ... (als zusammenhängender String!)
            $slug = trim($slug);                                                                            // entferne Leerzeichen aus Parameter-String
            $slug =  htmlspecialchars($slug);                                                               // entferne HTML spezifische Parameter aus Parameter-String
            $this->urlParameters = explode('/', $slug);                                            // Erstelle einen Parameter Array
            if ( count($this->urlParameters) > 1 && strtolower($this->urlParameters[0]) == 'backend' ) {    // Wenn Parameter vorhanden sind und der erste Parameter ein "backend" ist ...
                $this->isBackend = true;                                                                    // ... sage der App dass es ein Backend Modul ist
                $this->currentModule = $this->urlParameters[1];                                             // ... setze zweiten Parameter als aktuelles Modul
            } else {                                                                                        // ... sonst
                $this->currentModule = count($this->urlParameters) > 0 ? $this->urlParameters[0] : '';      // ... setze ersten Parameter als aktuelles Modul
            }
        } else {
            $this->currentModule = "index";                                                                 // Sollten keine Parameter vorhanden sein, setze "index" als aktuelles Modul (Backup!)
        }
    }

    private function module_getFileAndClass(string $moduleName) : array {
        $response = ["fileName" => '', "className" => ''];

        // Erkenne Dateiname
        if ( $moduleName && trim($moduleName) != '') {

            // Core File
            $className = "modules/core/{$moduleName}/module";
            $fileName = $_SERVER["DOCUMENT_ROOT"] . "/" . strtolower($className) . ".php";
            if ( file_exists($fileName)) {
                $response["fileName"] = strtolower($className) . ".php";
                $response["className"] = "modules\\core\\{$moduleName}\\module";
                return $response;
            }

            // File
            $className = "modules/{$moduleName}/module";
            $fileName = $_SERVER["DOCUMENT_ROOT"] . "/" . strtolower($className) . ".php";
            if ( file_exists($fileName)) {
                $response["fileName"] = strtolower($className) . ".php";
                $response["className"] = "modules\\{$moduleName}\\module";
                return $response;
            }

        }

        return $response;
    }

    // Starten des Moduls
    private function module_load(): bool {
        // Auslesen des aktuellen Moduls
        $fileAndClass = $this->module_getFileAndClass($this->currentModule);
        // Wenn Modul gefunden wurde
        if ( $fileAndClass && count($fileAndClass) > 0 && $fileAndClass["fileName"] && trim($fileAndClass["fileName"]) != '' && $fileAndClass["className"] && $fileAndClass["className"] != '' ) {
            // Setze und starte Klasse (Datei wird automatisch über die "Autoload" Funktion geladen!
            $className = $fileAndClass["className"];
            $module = new $className($this);

            // Wenn kein Modul gefunden ODER wenn keine Startfunktion vorhanden ist, "false" aus
            if ( !$module || !method_exists($module, 'start' )) { return false; }


            // Starte Modul
            $module->start();
            return true;

        } else {
            return false;
        }
    }
}

