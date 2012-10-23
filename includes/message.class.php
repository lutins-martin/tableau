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
    const QUERY_LOAD_NOUVEAU = "select TITRE from MESSAGES where TITRE like 'nouveau message%' ORDER BY TITRE DESC limit 1" ;

    public function __construct($id=null)
    {
        $firePHP = FirePHP::getInstance() ;
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
        else
        {
            $loadStm = $this->db->query(self::QUERY_LOAD_NOUVEAU) ;
            $lastTitre = $loadStm->fetch(PDO::FETCH_COLUMN) ;
            $firePHP->log($lastTitre,'last id') ;
            preg_match("/\((\d+)\)$/",$lastTitre,$lastId) ;
            $firePHP->log($lastId,'last id') ;
            if(isset($lastId[1])) $Id=$lastId[1]+1;
            else $Id=1 ;
            $this->titre = "nouveau message ($Id)" ;
            $this->debut = time() ;
            $this->fin = time() ;
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

    public function save()
    {
        $firePHP = FirePHP::getInstance() ;
        $values[":titre"] = $this->titre ;
        $values[":message"] = $this->message ;
        $values[":debut"] = strftime("%F",$this->debut) ;
        $values[":fin"] = strftime("%F",$this->fin) ;
        if($this->loaded)
        {
            $values[':rowid'] = $this->id ;
            $query = "update MESSAGES set TITRE=:titre,MESSAGE=:message,DEBUT=:debut,FIN=:fin where rowid=:rowid" ; //self::UPDATE ;
        }
        else
            $query = "insert into MESSAGES (TITRE,MESSAGE,DEBUT,FIN) values (:titre,:message,:debut,:fin)" ; //self::INSERT ;
        $saveStatement = $this->db->prepare($query) ;
        $saveStatement->execute($values) ;
        $rowId=$saveStatement->fetch(PDO::FETCH_COLUMN) ;
        if($rowId) $this->id = $rowId ;
    }

    public function delete()
    {
        $deleteStm = $this->db->prepare("delete from MESSAGES where ROWID=:messageId") ;
        $deleteStm->execute(array(":messageId" => $this->id)) ;
        $this->loaded = false ;
    }


    public function getId()
    {
        return $this->id ;
    }

    public function getTitre()
    {
        return $this->titre ;
    }

    public function setTitre($titre)
    {
        if(!is_null($titre)) $this->titre = $titre;
    }

    public function getMessage()
    {
        return $this->message ;
    }

    public function setMessage($message)
    {
        if(!is_null($message)) $this->message = $message ;
    }

    public function getDebut($formatted=false)
    {
        if($formatted) return strftime("%F",$this->debut) ;
        else return $this->debut ;
    }

    public function setDebut($debutEnTexte)
    {
        if(!is_null($debutEnTexte))
        {
            $debut = strtotime($debutEnTexte) ;
            if($debut!==FALSE) $this->debut = $debut ;
            else $this->debut = time() ;
        }
    }

    public function setFin($finEnTexte)
    {
        if(!is_null($finEnTexte))
        {
            $fin = strtotime($finEnTexte) ;
            if(($fin!==FALSE) && ($fin >= $this->debut) ) $this->fin = $fin ;
            else $this->fin = $this->debut ;
        }
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