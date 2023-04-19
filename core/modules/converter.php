<?php

/**
 * Allgemeine Klasse für diverse Converter
 */

namespace core\modules;

class Converter {

    /**
     * Konvertiert einen XML String in einen Array
     * @param string $xml XML String
     * @return array
     */
    static function XMLToArray(string $xml): array {
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml, JSON_PRETTY_PRINT);
        return json_decode($json, true);
    }

    /**
     * Konvertiert eine XML-Datei in einen Array
     * @param string $fileName Dateiname
     * @return array
     */
    static function XMLFileToArray(string $fileName): array {
        $xml = simplexml_load_file($fileName, null);
        $json = json_encode($xml, JSON_PRETTY_PRINT);
        return json_decode($json, true);
    }
}