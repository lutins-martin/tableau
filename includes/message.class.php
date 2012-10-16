<?php
class Message
{
    protected $db ;
    protected $loaded ;
    protected $message ;
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
        if(is_array($databaseRecord))
        {
            $this->id=$databaseRecord['ROWID'] ;
            $this->message=$databaseRecord['MESSAGE'] ;
            $this->debut = strtotime($databaseRecord['DEBUT']) ;
            $this->fin = strtotime($databaseRecord['FIN']) ;
            $this->loaded = true ;
        }
    }
}