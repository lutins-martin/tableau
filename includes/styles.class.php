<?php
class Styles
{

    private static $listeStyles ;


    const CREATESTYLES = "create table STYLES (NOM text,FICHIER text,ACTIF boolean)" ;
    const SELECTSTYLES = "select ROWID from (select NOM,FICHIER,ACTIF,ROWID from STYLES order by NOM asc)" ;

    const CREATELASTSTYLECHANGE = "create table DERNIERCHANGEMENT (HEUREDATE timestamp)" ;
    const SELECTLASTSTYLECHANGE = "select HEUREDATE from DERNIERCHANGEMENT" ;

    private static $m_pInstance;
    private static $db ;

    private function __construct()
    {
        self::$db = Database::getInstance() ;
        /* est-ce que la table Styles existe? */
        $existsStm = self::$db->query("select NAME from SQLITE_MASTER where TYPE='table' and NAME='STYLES'") ;
        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0) self::$db->query(self::CREATESTYLES) ;

        $existsStm = self::$db->query("select NAME from SQLITE_MASTER where TYPE='table' and NAME='DERNIERCHANGEMENT'") ;

        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0)
        {
            $stm = self::$db->query(self::CREATELASTSTYLECHANGE) ;
            $stm = self::$db->query("insert into DERNIERCHANGEMENT (HEUREDATE) values (datetime('now','localtime'))") ;
        }
    }

    public static function setActif($nouveauNomDeFichier)
    {
        $actifStm=self::$db->query("update STYLES set ACTIF=0") ;
        $actifStm=self::$db->prepare("update STYLES set ACTIF=1 where FICHIER=:fichier  ") ;
        $actifStm->execute(array(":fichier" => $nouveauNomDeFichier)) ;

        $actifStm=self::$db->query("update DERNIERCHANGEMENT set HEUREDATE=datetime('now','localtime')") ;
        
    }

    public static function getUnStyle($id)
    {
        if(!isset(self::$listeStyles[$id]))
        {
            $style = new Style($id) ;
            if($style->isLoaded())
            {
                self::$listeStyles[$id] = $style;
            }
        }

        if(isset(self::$listeStyles[$id])) return self::$listeStyles[$id] ;
        else return new Style() ;

    }

    public static function getInstance()
    {
        if (!self::$m_pInstance)
        {
            self::$m_pInstance = new Styles();
        }

        return self::$m_pInstance;
    }

    public static function getFichierStyleActif()
    {
        $db = Database::getInstance() ;
        $actifStm = $db->query("select rowid from STYLES where ACTIF=1") ;
        $styleId = $actifStm->fetch(PDO::FETCH_COLUMN) ;
        if($styleId!==false)
        {
            $style= self::getUnStyle($styleId) ;

            return "styles/".$style->getNomDeFichier() ;
        }
        return "styles/style.css" ;
    }
    
    public static function getFichierBackgroundActif()
    {
        Database::getInstance()->query("select rowid from STYLES where ACTIF=1") ;
        $styleId = $actifStm->fetch(PDO::FETCH_COLUMN) ;
        if($styleId!==false)
        {
            $style= self::getUnStyle($styleId) ;
        
            return "styles/".$style->getNomDeFichier() ;
        }
        return "styles/style.css" ;        
    }

    public static function getLesStyles()
    {
        $lesStylesStm = self::$db->query(self::SELECTSTYLES) ;

        $lesUid = $lesStylesStm->fetchAll(PDO::FETCH_COLUMN) ;

        foreach($lesUid as $uid)
        {
            if (!isset(self::$listeStyles[$uid])) self::$listeStyles[$uid] = new Style($uid) ;
        }
        return self::$listeStyles ;
    }

    public function register(Style $style)
    {
        if (!isset(self::$listeStyles[$style->getId()])) self::$listeStyles[$style->getId()] = $style ;
    }

}
