<?php

/**
 *  Allgemeine Startdatei
 *  Hier werden nur die Autoloader und die Globale App geladen. Alles andere wird aktuell über die Globale App behandelt
 *
 *  der eigentliche Start ist also in "core/App::__constructor"
 */

// Lade Autoloader damit alle Klassen direkt geladen werden können
require_once "core/autoload.php";
use core\App;

// Starte Anwendung
$app = new App();