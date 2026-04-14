<?php

class Database
{

    private static $pdo = null;

    public static function getConnection()
    {

        if (self::$pdo === null) {

            self::$pdo = new PDO(
                "mysql:host=localhost;dbname=projet_web;charset=utf8",
                "root",
                ""
            );

            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}
