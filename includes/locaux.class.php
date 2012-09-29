<?php
class Locaux
{

    private static $listeLocaux ;

    const CREATELOCAUX = "create table LOCAUX (NOM text)" ;
    const SELECTLOCAUX = "select ROWID from LOCAUX" ;

    const CREATEEDUCATRICES_DANS_LOCAUX = "create table EDUCATRICES_DANS_LOCAUX
    (EDUCATRICE_ROWID,LOCAL_ROWID" ;
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
        AND name='EDUCATRICES_DANS_LOCAUX'") ;
        $existsRec = $existsStm->fetachAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0) self::$db->query(self::CREATEEDUCATRICES_DANS_LOCAUX) ;

        $firePHP = FirePHP::getInstance() ;
        $firePHP->log(self::CREATELOCAUX,'CREATELOCAUX') ;
        $lesLocauxStm = self::$db->query(self::SELECTLOCAUX) ;
        $firePHP->log(self::SELECTLOCAUX,'SELECTLOCAUX all') ;
        $lesUid = $lesLocauxStm->fetchAll(PDO::FETCH_COLUMN) ;

        $firePHP->log($lesUid,'les UID') ;
        foreach($lesUid as $uid)
        {
            self::$listeLocaux[$uid] = new Local($uid) ;
        }
    }

    public static function getInstance()
    {
        if (!self::$m_pInstance)
        {
            self::$m_pInstance = new Locaux();
        }

        return self::$m_pInstance;
    }

    public static function getLesLocaux()
    {
        return self::$listeLocaux ;
    }

}