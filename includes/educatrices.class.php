<?php
class Educatrices
{

    private static $listeEducatrices ;

    const CREATE = "create table EDUCATRICES (NOM text)" ;
    const SELECT = "select ROWID from EDUCATRICES" ;
    // Store the single instance of Database
    private static $m_pInstance;
    private static $db ;

    private function __construct()
    {
        self::$db = Database::getInstance() ;
        /* est-ce que la table existe? */
        $existsStm = self::$db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='EDUCATRICES'") ;
        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0)
        {
            self::$db->query(self::CREATE) ;
        }

        $firePHP = FirePHP::getInstance() ;
        $firePHP->log(self::CREATE,'create') ;
        $lesEducatricesStm = self::$db->query(self::SELECT) ;
        $firePHP->log(self::SELECT,'select all') ;
        $lesUid = $lesEducatricesStm->fetchAll(PDO::FETCH_COLUMN) ;

        foreach($lesUid as $uid)
        {
            self::$listeEducatrices[$uid] = new Educatrice($uid) ;
        }
    }

    public static function getInstance()
    {
        if (!self::$m_pInstance)
        {
            self::$m_pInstance = new Educatrices();
        }

        return self::$m_pInstance;
    }

    public static function getLesEducatrices()
    {
        return self::$listeEducatrices ;
    }

}