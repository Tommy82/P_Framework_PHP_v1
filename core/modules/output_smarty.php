<?php

namespace core\modules;

// Einbinden der externen Smarty Klasse
require "vendor/smarty/libs/Smarty.class.php";

use Exception as ExceptionAlias;
use Smarty;

class Output_Smarty
{
    /** @var Smarty $smarty Smarty Klasse */
    private Smarty $smarty;
    /** @var array $variables Variablen, welche an das Template übergeben werden sollen */
    private array $variables;
    /** @var string $template Template welches geladen werden soll */
    private string $template;
    private bool $isBackend;
    private bool $useBasic;

    /**
     * Initialisieren des Smarty Templates
     * @param Config $config Konfiguration
     * @param bool $isBackend Gibt, an ob die aufgerufene Seite eine Backendseite ist
     */
    public function __construct(Config $config, $useBasic = true, bool $isBackend = false) {

        $this->variables = [];
        $this->isBackend = $isBackend;
        $this->useBasic = $useBasic;

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($config->smarty["template_dir"]);
        $this->smarty->setCompileDir($config->smarty["compile_dir"]);
        $this->smarty->setCacheDir($config->smarty["cache_dir"]);
        $this->smarty->setConfigDir($config->smarty["config_dir"]);
    }

    /**
     * Setzen des Standard-Templates
     * @param string $fileName
     * @return void
     */
    public function template_set(string $fileName): void
    {
        $this->template = $fileName;
    }

    /**
     * Setzen einer Variable
     * @param string $name Name der Variable
     * @param mixed $value Wert der Variable
     * @return void
     */
    public function variable_set(string $name,  mixed $value): void
    {
        $this->variables[$name] = $value;
    }

    /**
     * Erweitern einer Variable
     * - Falls Variable schon vorhanden wird der Wert hinten angehängt
     * - Falls Variable noch nicht vorhanden wird diese neue gesetzt
     * @param $name
     * @param $value
     * @return void
     */
    public function variable_append($name, $value): void
    {
        if ( !$this->variables[$name] ) {
            $this->variables[$name] = $value;
        } else {
            $this->variables[$name] .= $value;
        }
    }

    /**
     * Erstellen der Smarty Ausgabe
     * @param string|null $fileName Name der Template Datei (falls nicht angegeben, muss vorher über "template_set" ein template gesetzt werden!
     * @return void
     */
    public function output(?string $fileName = null): void
    {
        try {

            // Setzen des Templates, falls vorhanden
            if ( $fileName ) {
                $this->template_set($fileName);
            }

            // Prüfe alle Core Variablen
            $this->checkCoreVariables();

            // Setzen der Variablen
            if ( $this->variables && count($this->variables) > 0 ) {
                foreach (array_keys($this->variables) as $key ) {
                    $this->smarty->assign($key, $this->variables[$key]);
                }
            }

            // Setzen des Templates
            if ( $this->template ) {
                $this->smarty->display($this->template);
            }
        } catch (ExceptionAlias $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Prüfung aller benötigten Core Variablen
     * @return void
     */
    private function checkCoreVariables(): void
    {
        // Title
        if ( !array_key_exists('TITLE', $this->variables)) { $this->variable_set('TITLE', 'Kein Header angegeben');}

        // Parent Page
        if ( !array_key_exists('PARENT', $this->variables)) {
            if ( $this->useBasic ) {
                if ( $this->isBackend ) {
                    $this->variable_set('FILE_PARENT', 'basics/backend.tpl');
                } else {
                    $this->variable_set('FILE_PARENT', 'basics/frontend.tpl');
                }
            }
        }
    }
}