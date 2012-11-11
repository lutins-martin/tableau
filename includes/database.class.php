<?php
class Database
{
    // Store the single instance of Database
    private static $m_pInstance;
    private static $db ;

    private function __construct()
    {
        global $tableauRootDir ;
        self::$db = new PDO("sqlite:$tableauRootDir/tableau/tableau.sqlite") ;
        self::$db->query("PRAGMA synchronous=OFF") ;
    }

    public static function getInstance()
    {
        if (!self::$m_pInstance)
        {
            self::$m_pInstance = new Database();
        }

        return self::$m_pInstance;
    }

    public static function query($query)
    {
        return self::$db->query($query) ;
    }

    public static function prepare($query)
    {
        return self::$db->prepare($query) ;
    }

    public static function execute($values)
    {
        return self::$db->execute($values) ;
    }

    public static function fetch($parm)
    {
        return self::$db->fetch($parm) ;
    }

    public static function fetchAll($parm)
    {
        return self::$db->fetchAll($parm) ;
    }

}