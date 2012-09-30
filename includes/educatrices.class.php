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

        $lesEducatricesStm = self::$db->query(self::SELECT) ;
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

    public static function getUneEducatrice($id)
    {
        if(isset(self::$listeEducatrices[$id])) return self::$listeEducatrices[$id] ;
        else return new Educatrice() ;
    }

    public static function getLesEducatrices()
    {
        return self::$listeEducatrices ;
    }

    public function register(Educatrice $educatrice)
    {
        if (!isset(self::$listeEducatrices[$educatrice->getId()])) self::$listeEducatrices[$educatrice->getId()] = $educatrice ;
    }
}