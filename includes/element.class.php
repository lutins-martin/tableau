<?php
abstract class Element
{
    protected $db ;
    protected $nom ;
    protected $id ;
    protected $loaded = false ;

    protected $querySelectParId;
    protected $querySelectParNom ;
    protected $queryInsert ;
    protected $queryUpdate ;
    protected $queryDelete ;

    protected function __construct($nomOrId,$querySelectParNom,
    $querySelectParId,$queryInsert,$queryUpdate,$queryDelete)
    {
        $this->db = Database::getInstance() ;

        $this->querySelectParId= $querySelectParId;
        $this->querySelectParNom = $querySelectParNom ;
        $this->queryInsert = $queryInsert ;
        $this->queryUpdate = $queryUpdate ;
        $this->queryDelete = $queryDelete ;

        if(!is_null($nomOrId))
        {
            if(is_numeric($nomOrId))
            {
                /* recuperer par index */
                $query = $this->querySelectParId ; //self::SELECT_PAR_UID ;
                $values[":rowid"] = $nomOrId ;
            }
            else
            {
                /* recupere par nom */
                $query = $this->querySelectParNom ;//self::SELECT_PAR_NOM ;
                $values[":nom"] = $nomOrId ;
            }

            $loadStatement = $this->db->prepare($query) ;
            $loadStatement->execute($values) ;
            $record = $loadStatement->fetch(PDO::FETCH_ASSOC) ;

            $this->loadFromDatabaseRecord($record) ;
        }
        else
        {
            $this->nom="&nbsp;" ;
            $this->id=0 ;
        }
    }

    public function delete()
    {
        $deleteStm = $this->db->prepare($this->queryDelete) ;
        $deleteStm->execute(array(":rowid" => $this->id)) ;
        $this->loaded = false ;
    }

    public function loadFromDatabaseRecord($record)
    {
        if (isset($record['NOM']))
        {
            $this->nom = $record['NOM'] ;
            $this->id = $record['rowid'] ;
            $this->loaded = true ;
        }
    }

    public function getNom()
    {
        return $this->nom ;
    }

    public function setNom($nom)
    {
        $this->nom = $nom ;
    }

    public function save($valueParms=null)
    {
        $firePHP = FirePHP::getInstance() ;
        $firePHP->log($this,'element') ;
        $values[":nom"] = $this->nom ;
        if(is_array($valueParms))
        {
            foreach($valueParms as $nom => $valeur)
            $values[$nom] = $valeur ;
        }
        $firePHP->log($values,'values') ;
        if($this->loaded)
        {
            $values[':rowid'] = $this->id ;
            $query = $this->queryUpdate ; //self::UPDATE ;
        }
        else
            $query = $this->queryInsert ; //self::INSERT ;
        $firePHP->log($query,'query') ;
        $saveStatement = $this->db->prepare($query) ;
        $saveStatement->execute($values) ;
        $rowId=$saveStatement->fetch(PDO::FETCH_COLUMN) ;
        if($rowId) $this->id = $rowId ;
    }

    public function getId()
    {
        return $this->id ;
    }

    public function isLoaded()
    {
        return $this->loaded ;
    }
}