<?php
class Locaux
{

    private static $listeLocaux ;

    const CREATELOCAUX = "create table LOCAUX (NOM text)" ;
    const SELECTLOCAUX = "select ROWID from LOCAUX" ;

    const CREATEGROUPES_DANS_LOCAUX = "create table GROUPES_DANS_LOCAUX
    (GROUPE number,LOCAL number)" ;
    // Store the single instance of Database
    private static $m_pInstance;
    private static $db ;

    private function __construct()
    {
        self::$db = Database::getInstance() ;
        /* est-ce que la table LOCAUX existe? */
        $existsStm = self::$db->query("select name FROM sqlite_master WHERE type='table' AND name='LOCAUX'") ;
        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0) self::$db->query(self::CREATELOCAUX) ;

        $existsStm = self::$db->query("select name from sqlite_master where type='table'
        AND name='GROUPES_DANS_LOCAUX'") ;
        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0) self::$db->query(self::CREATEGROUPES_DANS_LOCAUX) ;

    }

    public static function getInstance()
    {
        if (!self::$m_pInstance)
        {
            self::$m_pInstance = new Locaux();
        }

        return self::$m_pInstance;
    }

    public static function getUnLocal($id)
    {
        if(!isset(self::$listeLocaux[$id]))
        {
            $local = new Local($id) ;
            if ($local->isLoaded()) self::$listeLocaux[$id] = $local ;
        }

        if(isset(self::$listeLocaux[$id])) return self::$listeLocaux[$id] ;
        else return false ;
    }

    public static function getLesLocaux()
    {
        $lesLocauxStm = self::$db->query(self::SELECTLOCAUX) ;
        $lesUid = $lesLocauxStm->fetchAll(PDO::FETCH_COLUMN) ;

        foreach($lesUid as $uid)
        {
            if (!isset(self::$listeLocaux[$uid])) self::$listeLocaux[$uid] = new Local($uid) ;
        }

        return self::$listeLocaux ;
    }

    public function register(Local $local)
    {
        if (!isset(self::$listeLocaux[$local->getId()])) self::$listeLocaux[$local->getId()] = $local;
    }
}