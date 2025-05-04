<?php

namespace oml\php\core;

use PDO;
use Throwable;

class Database
{
    public static ?PDO $PDO = null;

    /**
     * Initializes the PDO instance if it has not already been initialized
     *
     * @return void
     */
    public static function initializeDatabase()
    {
        if (!isset(Database::$PDO)) {
            Database::$PDO = new PDO(OML_CONNECTION_STRING, DB_USER, DB_PASSWORD);
            Database::$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * Applies database migrations using migration files found in OML_SQL_DIR
     *
     * @return void
     */
    public static function upgradeDatabase()
    {
        self::initializeDatabase();

        $migrationList = self::selectMigrations();
        $files = scandir(OML_SQL_DIR);

        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            if (!in_array($fileInfo['filename'], $migrationList, true)) {
                if (strtolower($fileInfo['extension']) === 'sql') {
                    self::applyMigration($fileInfo['filename']);
                }
            }
        }
    }

    /**
     * Tries to execute the SQL query to select all migration names. If an error occurs,
     * the function will return an empty array
     *
     * @return string[] The files names of the applied migrations.
     */
    public static function selectMigrations()
    {
        try {
            $sqlQuery = "SELECT `name` FROM " . OML_SQL_MIGRATION_TABLENAME;
            $statement = self::$PDO->query($sqlQuery);
            return $statement->fetchAll(PDO::FETCH_COLUMN);
        } catch (Throwable $exception) {
            return [];
        }
    }

    /**
     * Tries to execute the SQL migration from OML_SQL_DIR. If an error occurs, the script will die and
     * the error message will be displayed.
     *
     * @param string $filename The name of the SQL migration file to apply
     *
     * @return void
     */
    public static function applyMigration(string $filename)
    {
        $sqlQuery = "INSERT INTO " . OML_SQL_MIGRATION_TABLENAME . " (`name`) VALUES (:name)";
        $sqlMigration = file_get_contents(OML_SQL_DIR . DIRECTORY_SEPARATOR . $filename . ".sql");

        try {
            self::$PDO->query($sqlMigration);
            $statement = self::$PDO->prepare($sqlQuery);
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
