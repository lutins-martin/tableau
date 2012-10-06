<?php
class Local extends Element
{

    private $groupes ;

    const SELECT_PAR_NOM = "select NOM,ROWID from LOCAUX where NOM = :nom" ;
    const SELECT_PAR_ROWID = "select NOM,ROWID from LOCAUX where ROWID= :rowid" ;

    const INSERT = "insert into LOCAUX (NOM) values (:nom)" ;
    const UPDATE = "update LOCAUX set NOM=:nom where ROWID=:rowid" ;
    const DELETE = "delete from LOCAUX where ROWID=:rowid" ;

    public function __construct($nomOrId=null)
    {
        parent::__construct($nomOrId,self::SELECT_PAR_NOM,
        self::SELECT_PAR_ROWID,self::INSERT,self::UPDATE,self::DELETE) ;
    }

    public function delete()
    {
        $deleteStm = $this->db->prepare("delete from GROUPES_DANS_LOCAUX where LOCAL=:rowid") ;
        $deleteStm->execute(array(":rowid" => $this->id)) ;
        parent::delete() ;
    }

    public function getGroupes()
    {
        if (!isset($this->groupes) && !is_array($this->groupes))
        {
            $groupeStm = $this->db->prepare("select GROUPE from GROUPES_DANS_LOCAUX where LOCAL=:local") ;
            $groupeStm->execute(array(":local" => $this->id)) ;
            $groupesIdListe = $groupeStm->fetchAll(PDO::FETCH_COLUMN) ;

            if (is_array($groupesIdListe) && count($groupesIdListe))
            {
                foreach($groupesIdListe as $groupeId)
                {
                    $groupe = new Groupe($groupeId) ;

                    if ($groupe->isLoaded()) $this->groupes[$groupe->getId()] = $groupe ;
                }
            }
        }
        if(isset($this->groupe)) return $this->groupe ;
        else return false ;
    }

    public function addGroupe(Groupe $groupe)
    {
        if(!isset($this->groupes[$groupe->getId()])) $this->groupes[$groupe->getId()] = $groupe ;
    }

    private function registerToLocaux()
    {
        $locaux = Locaux::getInstance() ;
        $locaux->register($this) ;
    }

}