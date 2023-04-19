<?php

namespace core\modules;

use mysqli as mysqliAlias;
use Throwable as ThrowableAlias;

class Database
{
    /** @var String $host Ort der Datenbank */
    private String $host;
    /** @var Int $port Port für die Datenbank (Default: 3306) */
    private Int $port;
    /** @var String $name Name der Datenbank */
    private String $name;
    /** @var String|null $user Benutzer mit Zugriff auf die Datenbank */
    private ?String $user;
    /** @var String|null $pass Kennwort des Datenbankbenutzers */
    private ?String $pass;
    /** @var Int|null $socket Falls benötigt kann hier der Socket ausgewählt werden */
    private ?Int $socket;
    /** @var mysqliAlias $connection Datenbankverbindung */
    private mysqliAlias $connection;
    public bool $isConnected;

    /**
     * Herstellen einer Datenbankverbindung
     * @param \core\modules\Config|null $config
     */
    public function __construct(?Config $config) {
        $this->isConnected = false;
        if ( $this->checkParameter($config) ) {
            if ($this->connect()) {
                $this->isConnected = true;
            }
        }
    }

    /**
     * Prüfen der Parameter zum erfolgreichen Herstellen einer Verbindung
     * @param Config|null $config Konfigurationsdaten
     * @return bool "true" = Alle Parameter in Ordnung / "false" = Parameter fehlerhaft
     */
    private function checkParameter(?Config $config): bool
    {
        try {

            // Abbrechen, wenn keine Konfiguration angegeben
            if ( !$config || !$config->database ) {
                return false;
            }

            // Prüfen und setzen der benötigten Daten
            $this->host = array_key_exists("host", $config->database) ? $config->database["host"] : null;
            $this->port = array_key_exists("port", $config->database) ? $config->database["port"] : 3306;
            $this->name = array_key_exists("name", $config->database) ? $config->database["name"] : null;
            $this->user = array_key_exists("user", $config->database) ? $config->database["user"] : null;
            $this->pass = array_key_exists("pass", $config->database) ? $config->database["pass"] : null;
            $this->socket = array_key_exists("socket", $config->database) ? $config->database["socket"] : null;

            return true;
        } catch (ThrowableAlias $e ) {
            echo $e->getMessage();
            return false;
        }

    }

    /**
     * Herstellen der Datenbankverbindung
     * @return bool "true" = Verbindung erfolgreich / "false" = Verbindung NICHT erfolgreich
     */
    private function connect(): bool
    {
        $this->connection = new mysqliAlias($this->host, $this->user, $this->pass, $this->name, $this->port, $this->socket);
        if ( !$this->connection->connect_error ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prüfung des SQL Strings
     * @param $sql
     * @return bool
     */
    private function checkSql($sql) : bool {
        return true;
    }

    /**
     * Prüfung der Datenbankverbindung
     * @return bool
     */
    private function checkConnection(): bool {
        if ( !$this->isConnected || $this->connection->connect_error ) {
            return false;
        }
        return true;
    }

    /**
     * Prüft, ob eine Datenbanktabelle existiert.
     * Falls nicht, wird diese angelegt
     * @param string $tableName Name der Tabelle
     * @return bool
     */
    public function CheckTable(string $tableName): bool
    {
        try {
            // Auslesen aller Tabellen in der Datenbank
            $queryResult = $this->connection->query("SHOW TABLES like '$tableName'");
            if ( $queryResult ) {
                // Durchlaufe alle gefundenen Tabellen
                $result = $queryResult->fetch_array();
                if ( $result && count($result) > 0 ) {
                    for ( $i = 0; $i < count($result); $i++ ) {
                        // Wenn gefunden, beende Funktion
                        if ( $result[$i] == $tableName) {
                            return true;
                        }
                    }
                }
            }

            // Erstelle Tabelle in Datenbank. Lege 2 Standardfelder an "id", "date_update"
            $sql = "CREATE TABLE `$tableName` ( id INT(11)  UNSIGNED AUTO_INCREMENT PRIMARY KEY, date_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
            // Rückmeldung ib erfolgreich angelegt
            if ( $this->connection->query($sql) ) { return true; }
            return false;
        } catch ( \Throwable $e ) {
            //echo "<br><br>" . $e->getMessage() . "<br>";
            //echo "<br><br>" . $e->getFile() . ": " . $e->getLine() . "<br>";
            // TODO: Fehler einfügen
            return false;
        }
    }

    public function CheckColumn(string $tableName, string $fieldID, string $fieldType, string $default = '', string $foreignTable = null, string $foreignColumn = null): bool
    {
        try {
            $result = $this->connection->query("SHOW COLUMNS FROM `$tableName` LIKE '$fieldID'");
            $exists = (bool)mysqli_num_rows($result);

            if ( $exists ) {
                $sql = "ALTER TABLE $tableName MODIFY $fieldID $fieldType $default";
            } else {
                $sql = "ALTER TABLE $tableName ADD $fieldID $fieldType $default";
            }

            // Update der Tabelle
            if ( $this->connection->query($sql) ) {
                // prüfe auf Keys
                if ( $foreignTable && trim($foreignTable) != '' && $foreignColumn && trim($foreignColumn) != '' ) {

                    // Erstelle Contain für Foreign Key
                    $key = "FK_" . strtolower($foreignTable) . strtolower($foreignColumn) . "_" . strtolower($tableName) . strtolower($fieldID);

                    // Rufe Foreign Key aus Datenbank ab
                    $queryResult = $this->connection->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = '{$this->name}' AND CONSTRAINT_NAME = '$key' AND CONSTRAINT_TYPE   = 'FOREIGN KEY'");

                    // Falls nicht gefunden, erstelle neuen Foreign Key
                    if ( !$queryResult ) {
                        $this->connection->query("ALTER TABLE `$tableName` ADD CONSTRAINT `$key` FOREIGN KEY (`$fieldID`) REFERENCES `$foreignTable` (`$foreignColumn`)");
                    }
                }

                return true;
            }

            return false;

        } catch ( \Throwable $e ) {
            //echo "<br><br>" . $e->getMessage() . "<br>";
            //echo "<br><br>" . $e->getFile() . ": " . $e->getLine() . "<br>";
            // TODO: Fehler einfügen
            return false;
        }
    }


    /***
     * Auslesen eines einzelnen Datensatzes
     * @param String $sql SQL Abfrage
     * @return array|string|null
     */
    public function SelectRow(String $sql): array|string|null {
        if ( !$this->checkConnection() || !$this->checkSql($sql)) {
            return null;
        }

        $queryResult = $this->connection->query($sql);
        return $queryResult ? $queryResult->fetch_array(MYSQLI_ASSOC) : [];
    }

    /**
     * Auslesen mehrerer Datensätze als Array
     * @param String $sql
     * @return array|null
     * @noinspection PhpUnusedFunctionInspection
     * @noinspection PhpMissingStrictTypesDeclarationInspection
     */
    public function SelectArr(String $sql): array|null {
        if ( !$this->checkConnection() || !$this->checkSql($sql)) {
            return null;
        }

        $queryResult = $this->connection->query($sql);
        return $queryResult ? $queryResult->fetch_all(MYSQLI_ASSOC) : null;
    }

    /**
     * Auslesen mehrerer Datensätze als Klasse oder StdClass
     * @param String $sql
     * @param object|null $class
     * @return object|null
     */
    public function SelectObj(String $sql, ?object $class = null): object|null {
        if ( !$this->checkConnection() || !$this->checkSql($sql)) {
            return null;
        }

        $queryResult = $this->connection->query($sql);
        return $queryResult ? $queryResult->fetch_object($class) : null;
    }

    public function Insert(string $sql): int|string|null
    {
        if ( !$this->checkConnection() || !$this->checkSql($sql)) {
            return null;
        }

        $queryResult = $this->connection->query($sql);
        return $this->connection->insert_id;
    }
}