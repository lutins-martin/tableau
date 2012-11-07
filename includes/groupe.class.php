<?php
class Groupe extends Element
{

    private $educatrices ;
    private $local ;

    const SELECT_PAR_NOM = "select NOM,ROWID from GROUPES where NOM = :nom" ;
    const SELECT_PAR_ROWID = "select NOM,ROWID from GROUPES where ROWID= :rowid" ;

    const INSERT = "insert into GROUPES (NOM) values (:nom)" ;
    const UPDATE = "update GROUPES set NOM=:nom where ROWID=:rowid" ;
    const DELETE = "delete from GROUPES where ROWID=:rowid" ;

    public function __construct($nomOrId=null,Educatrice $educatrice=null)
    {
        parent::__construct($nomOrId,self::SELECT_PAR_NOM,
        self::SELECT_PAR_ROWID,self::INSERT,self::UPDATE,self::DELETE) ;
    }

    public function delete()
    {
        $deleteStm = $this->db->prepare("delete from EDUCATRICES_DANS_GROUPES where GROUPE=:rowid") ;
        $deleteStm->execute(array(":rowid" => $this->id)) ;

        $deleteStm = $this->db->prepare("delete from GROUPES_DANS_LOCAUX where GROUPE=:rowid") ;
        $deleteStm->execute(array(":rowid" => $this->id)) ;
        parent::delete() ;
    }

    private function registerToGroupes()
    {
        $groupes = Groupes::getInstance() ;
        $groupes->register($this) ;
    }

    public function save()
    {
        parent::save() ;

        if(isset($this->local) && ($this->local instanceof Local))
        {
            $existsGroupe_Dans_Local = $this->db->prepare("select LOCAL from GROUPES_DANS_LOCAUX where
            GROUPE=:groupe") ;
            $existsGroupe_Dans_Local->execute(array(":groupe" => $this->id)) ;

            if($existsGroupe_Dans_Local)
            {
                $localId = $existsGroupe_Dans_Local->fetch(PDO::FETCH_COLUMN) ;
                if($localId)
                $saveGroupe_Dans_Local = $this->db->query("update GROUPES_DANS_LOCAUX set LOCAL=:local
            where GROUPE=:groupe") ;
                else
                $saveGroupe_Dans_Local = $this->db->query("insert into GROUPES_DANS_LOCAUX
                (GROUPE,LOCAL) values (:groupe,:local)") ;
            }
            if($localId!=$this->local->getId())
            {
                $saveGroupe_Dans_Local->execute(array(":groupe" => $this->id,
                ":local" => $this->local->getId())) ;
            }

            $this->local->save() ;
        }
    }

    public function getLocal()
    {
        if (!isset($this->local) || !($this->local instanceof Local))
        {
            $localStm = $this->db->prepare("select LOCAL from GROUPES_DANS_LOCAUX where GROUPE=:groupe") ;
            if ($localStm)
            {
                $localStm->execute(array(":groupe" => $this->id)) ;
                $localId=$localStm->fetch(PDO::FETCH_COLUMN) ;
                if($localId)
                {
                    $locaux = Locaux::getInstance() ;
                    $local = $locaux->getunLocal($localId) ;
                    if($local instanceof Local) $this->local = $local ;
                }
            }
        }
        if(isset($this->local) && ($this->local instanceof Local)) return $this->local ;
        else return new Local() ;
    }

    public function setLocal(Local $local)
    {
        $this->local = $local ;
    }

    public function addEducatrice(Educatrice $educatrice)
    {
        if(!isset($this->educatrices[$educatrice->getId()])) $this->educatrices[$educatrice->getId()] = $educatrice ;
    }
}