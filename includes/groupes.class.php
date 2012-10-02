<?php
class Groupes
{

    private static $listeGroupes ;

    const CREATEGROUPES = "create table GROUPES (NOM text)" ;
    const SELECTGROUPES = "select ROWID from GROUPES" ;

    const CREATEEDUCATRICES_DANS_GROUPES = "create table EDUCATRICES_DANS_GROUPES
    (EDUCATRICE numeric,GROUPE numeric)" ;
    // Store the single instance of Database
    private static $m_pInstance;
    private static $db ;

    private function __construct()
    {
        self::$db = Database::getInstance() ;
        /* est-ce que la table Groupes existe? */
        $existsStm = self::$db->query("select name FROM sqlite_master WHERE type='table' AND name='GROUPES'") ;
        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0) self::$db->query(self::CREATEGROUPES) ;

        $existsStm = self::$db->query("select name from sqlite_master where type='table'
        AND name='EDUCATRICES_DANS_GROUPES'") ;
        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0) self::$db->query(self::CREATEEDUCATRICES_DANS_GROUPES) ;

    }

    public static function getUnGroupe($id)
    {
        if(!isset(self::$listeGroupes[$id]))
        {
            $groupe = new Groupe($id) ;
            if($groupe->isLoaded())
            {
                self::$listeGroupes[$id] = $groupe ;
            }
        }

        if(isset(self::$listeGroupes[$id])) return self::$listeGroupes[$id] ;
        else return new Groupe() ;

    }

    public static function getInstance()
    {
        if (!self::$m_pInstance)
        {
            self::$m_pInstance = new Groupes();
        }

        return self::$m_pInstance;
    }

    public static function getLesGroupes()
    {
        $lesGroupesStm = self::$db->query(self::SELECTGROUPES) ;

        $lesUid = $lesGroupesStm->fetchAll(PDO::FETCH_COLUMN) ;

        foreach($lesUid as $uid)
        {
            if (!isset(self::$listeGroupes[$uid])) self::$listeGroupes[$uid] = new Groupe($uid) ;
        }
        return self::$listeGroupes ;
    }

    public function register(Groupe $groupe)
    {
        if (!isset(self::$listeGroupes[$groupe->getId()])) self::$listeGroupes[$groupe->getId()] = $groupe ;
    }

}