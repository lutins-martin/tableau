<?php
class Styles
{

    private static $listeStyles ;

    const CREATESTYLES = "create table STYLES (NOM text,FICHIER text,ACTIF boolean)" ;
    const SELECTSTYLES = "select ROWID from (select NOM,FICHIER,ACTIF,ROWID from STYLES order by NOM asc)" ;

    private static $m_pInstance;
    private static $db ;

    private function __construct()
    {
        self::$db = Database::getInstance() ;
        /* est-ce que la table Styles existe? */
        $existsStm = self::$db->query("select NAME from SQLITE_MASTER where TYPE='table' and NAME='STYLES'") ;
        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0) self::$db->query(self::CREATESTYLES) ;

    }

    public static function setActif($id)
    {
        $actifStm=self::$db->query("update STYLES set ACTIF=false") ;
        $actifStm=self::$db->prepare("update STYLES set ACTIF=true where ROWID=:id") ;
        $actifStm->execute(array(":id" => $id)) ;
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