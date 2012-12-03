<?php
class Messages
{
    const QUERY_TODAYS_MESSAGE = "select ROWID,DEBUT,FIN,MESSAGE from MESSAGES where :today between DEBUT and FIN" ;
    const QUERY_ALL_MESSAGES = "select ROWID,DEBUT,FIN,MESSAGE from MESSAGES order by DEBUT asc" ;

    const QUERY_CREATE_MESSAGES  = "create table MESSAGES (TITRE text,MESSAGE text,DEBUT timestamp,FIN timestamp)" ;

    private static $db ;
    private static $m_pInstance;

    private static $lesMessages = array();
    private static $lesMessagesAujourdhui = array() ;

    private function __construct($today=false)
    {
        self::$db = Database::getInstance() ;
        /* est-ce que la table Messages existe? */
        $existsStm = self::$db->query("select name FROM sqlite_master WHERE type='table' AND name='MESSAGES'") ;
        $existsRec = $existsStm->fetchAll(PDO::FETCH_ASSOC) ;

        if(count($existsRec)==0) self::$db->query(self::QUERY_CREATE_MESSAGES) ;

        $firePHP = FirePHP::getInstance() ;

        try
        {
            $colonneExiste = self::$db->query("select MODIFIELE from MESSAGES") ;
            if(!($colonneExiste instanceof PDO))
            {
                $creerColonne = self::$db->query("alter table MESSAGES add column MODIFIELE timestamp") ;
            }
        }
        catch(Exception $e)
        {
            $firePHP->log($e,"exception") ;
        }

    }

    public static function getInstance()
    {
        if (!self::$m_pInstance)
        {
            self::$m_pInstance = new self();
        }

        return self::$m_pInstance;
    }

    public static function getUnMessage($messageID=null)
    {
        if(count(self::$lesMessages)==0) self::getLesMessages() ;
        if(is_null($messageID)) return new Message() ;
        if(isset(self::$lesMessages[$messageID])) return self::$lesMessages[$messageID] ;
        else return new Message() ;
    }

    public static function getLesMessages($aujourdhui=false)
    {
        if(count(self::$lesMessages)==0)
        {
            $tousLesMessageStm = self::$db->query("select ROWID,TITRE,MESSAGE,DEBUT,FIN,MODIFIELE from MESSAGES order by DEBUT asc") ;
            $lesMessagesBruts = $tousLesMessageStm->fetchAll(PDO::FETCH_ASSOC) ;
            foreach($lesMessagesBruts as $unMessageRec)
            {
                $message = new Message() ;
                $message->loadFromDatabaseRecord($unMessageRec) ;
                if($message->isLoaded()) self::$lesMessages[$message->getId()]=$message ;
            }

            $aujourdhuiStm= self::$db->prepare("select ROWID from MESSAGES where :aujourdhui between DEBUT and FIN") ;
            $aujourdhuiStm->execute(array(":aujourdhui" => strftime("%F"))) ;

            $lesMessagesDaujourdhui = $aujourdhuiStm->fetchAll(PDO::FETCH_COLUMN) ;
            foreach($lesMessagesDaujourdhui as $rowid)
            {
                if(isset(self::$lesMessages[$rowid]))
                self::$lesMessagesAujourdhui[$rowid] = self::$lesMessages[$rowid] ;
            }
        }

        if ($aujourdhui) return self::$lesMessagesAujourdhui ;
        else return self::$lesMessages ;
    }

}