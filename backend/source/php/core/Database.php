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
        if (isset(Database::$PDO) === false) {
            Database::$PDO = new PDO(___CONNECTION_STRING___, DB_USER, DB_PASSWORD);
            Database::$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public static function upgradeDatabase()
    {
        self::initializeDatabase();
        $migration_list = self::selectMigrations();
        $files = scandir(___SQL_DIRECTORY___);

        foreach ($files as $file) {
            $file_info = pathinfo($file);
            if (!in_array($file_info['filename'], $migration_list, true)) {
                if (strtolower($file_info['extension']) === 'sql') {
                    self::applyMigration($file_info['filename']);
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

    private static function applyMigration(string $file_name)
    {
        $sql_migration = file_get_contents(___SQL_DIRECTORY___ . DIRECTORY_SEPARATOR . $file_name . ".sql");

        try {
            self::$PDO->query($sql_migration);
            $statement = self::$PDO->prepare(self::$INSERT);
            $statement->bindValue(':name', $file_name);
            $statement->execute();
        } catch (Throwable $exception) {
            echo "<b>{$file_name}</b><br>"
                . "<pre>{$sql_migration}</pre><br>"
                . "<pre>{$exception->getMessage()}</pre><br>";
            wp_die();
        }
    }
}
