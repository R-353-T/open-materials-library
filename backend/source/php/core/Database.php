<?php

namespace oml\php\core;

use PDO;
use Throwable;

class Database
{
    public static ?PDO $PDO = null;
    private static string $SELECT = "SELECT `name` FROM " . ___DB_MIGRATION___;
    private static string $INSERT = "INSERT INTO " . ___DB_MIGRATION___ . " (`name`) VALUES (:name)";

    public static function initializeDatabase()
    {
        if (!isset(Database::$PDO)) {
            Database::$PDO = new PDO(___CONNECTION_STRING___, DB_USER, DB_PASSWORD);
            Database::$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public static function upgradeDatabase()
    {
        self::initializeDatabase();
        $migrationList = self::selectMigrations();
        $files = scandir(___SQL_DIRECTORY___);

        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            if (!in_array($fileInfo['filename'], $migrationList, true)) {
                if (strtolower($fileInfo['extension']) === 'sql') {
                    self::applyMigration($fileInfo['filename']);
                }
            }
        }
    }

    private static function selectMigrations()
    {
        try {
            $statement = self::$PDO->query(self::$SELECT);
            return $statement->fetchAll(PDO::FETCH_COLUMN);
        } catch (Throwable $exception) {
            return [];
        }
    }

    private static function applyMigration(string $filename)
    {
        $sqlMigration = file_get_contents(___SQL_DIRECTORY___ . DIRECTORY_SEPARATOR . $filename . ".sql");

        try {
            self::$PDO->query($sqlMigration);
            $statement = self::$PDO->prepare(self::$INSERT);
            $statement->bindValue(':name', $filename);
            $statement->execute();
        } catch (Throwable $exception) {
            echo "<b>{$filename}</b><br>"
                . "<pre>{$sqlMigration}</pre><br>"
                . "<pre>{$exception->getMessage()}</pre><br>";
            wp_die();
        }
    }
}
