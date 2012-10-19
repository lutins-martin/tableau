<?php
class Message
{
    protected $db ;
    protected $loaded ;
    protected $message ;
    protected $titre ;
    protected $debut ;
    protected $fin ;
    protected $id ;

    const QUERY_LOAD = "select ROWID,MESSAGE,DEBUT,FIN from MESSAGES where ROWID=:id" ;

    public function __construct($id=null)
    {
        $this->db=Database::getInstance() ;
        if(!is_null($id))
        {
            $loadStm = $this->db->prepare(self::QUERY_LOAD) ;
            $loadStm->execute(array(":id" => $id)) ;

            $messageRecord = $loadStm->fetch(PDO::FETCH_ASSOC) ;
            if(is_array($messageRecord) && count($messageRecord)==1)
            {
                $this->loadFromDatabaseRecord($messageRecord) ;
            }
        }

    }

    public function loadFromDatabaseRecord($databaseRecord)
    {
        $firePHP = FirePHP::getInstance() ;
        $firePHP->trace(__METHOD__) ;
        $firePHP->log($databaseRecord,'database record') ;
        if(is_array($databaseRecord))
        {
            $this->id=$databaseRecord['rowid'] ;
            $this->message=$databaseRecord['MESSAGE'] ;
            $this->titre=$databaseRecord['TITRE'] ;
            $this->debut = strtotime($databaseRecord['DEBUT']) ;
            $this->fin = strtotime($databaseRecord['FIN']) ;
            $this->loaded = true ;
        }
    }

    public function getId()
    {
        return $this->id ;
    }

    public function getTitre()
    {
        return $this->titre ;
    }

    public function getMessage()
    {
        return $this->message ;
    }

    public function getDebut($formatted=false)
    {
        if($formatted) return strftime("%F",$this->debut) ;
        else return $this->debut ;
    }

    public function getFin($formatted=false)
    {
        if($formatted) return strftime("%F",$this->fin) ;
        else return $this->fin ;
    }

    public function isLoaded()
    {
        return $this->loaded ;
    }
}