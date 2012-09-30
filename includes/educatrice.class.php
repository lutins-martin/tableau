<?php
class Educatrice extends Element
{

    private $groupe ;

    const SELECT_PAR_NOM = "select NOM,ROWID from EDUCATRICES where NOM = :nom" ;
    const SELECT_PAR_ROWID = "select NOM,ROWID from EDUCATRICES where ROWID= :rowid" ;

    const INSERT = "insert into EDUCATRICES (NOM) values (:nom)" ;
    const UPDATE = "update EDUCATRICES set NOM=:nom where ROWID=:rowid" ;
    const DELETE = "delete from EDUCATRICES where ROWID=:rowid" ;

    public function __construct($nomOrId=null)
    {
        parent::__construct($nomOrId,self::SELECT_PAR_NOM,
        self::SELECT_PAR_ROWID,self::INSERT,self::UPDATE,self::DELETE) ;

        $this->getGroupe() ;

    }

    public function getGroupe()
    {
        $firePHP = FirePHP::getInstance(true) ;
        $firePHP->setEnabled(true) ;
        if(!isset($this->groupe))
        {
            $groupeStm = $this->db->prepare("select GROUPE from EDUCATRICES_DANS_GROUPES where EDUCATRICE=:educatrice") ;
            $groupeStm->execute(array(":educatrice" => $this->id)) ;
            $groupeId=$groupeStm->fetch(PDO::FETCH_COLUMN) ;
            if($groupeId)
            {
                $groupes = Groupes::getInstance() ;
                $groupe = $groupes->getUnGroupe($groupeId) ;
                if($groupe->isLoaded())
                {
                    $this->groupe = $groupe ;
                    $this->groupe->addEducatrice($this) ;
                }
            }
        }
        if(isset($this->groupe)) return $this->groupe ;
        else return new Groupe();
    }

    public function getLocal()
    {
        return $this->getGroupe()->getLocal() ;
    }

    public function setLocal($localId)
    {
        if(isset($this->groupe) && ($this->groupe instanceof Groupe))
        {
            $locaux = Locaux::getInstance() ;
            $local = $locaux->getUnLocal($localId) ;
            $this->groupe->setLocal($local) ;
        }
    }

    public function save()
    {
        parent::save() ;

        if(isset($this->groupe) && ($this->groupe instanceof Groupe))
        {
            $existsEducatrice_dans_groupe = $this->db->prepare("select GROUPE from EDUCATRICES_DANS_GROUPES where
            EDUCATRICE=:educatrice") ;
            $existsEducatrice_dans_groupe->execute(array(":educatrice" => $this->id)) ;

            if($existsEducatrice_dans_groupe)
            {
                $groupeId = $existsEducatrice_dans_groupe->fetch(PDO::FETCH_COLUMN) ;
                if($groupeId)
                $saveEducatrice_dans_groupe = $this->db->query("update EDUCATRICES_DANS_GROUPES set GROUPE=:groupe
            where EDUCATRICE=:educatrice") ;
                else
                $saveEducatrice_dans_groupe = $this->db->query("insert into EDUCATRICES_DANS_GROUPES
                (EDUCATRICE,GROUPE) values (:educatrice,:groupe)") ;
            }
            if($groupeId!=$this->groupe->getId())
            {
                $saveEducatrice_dans_groupe->execute(array(":educatrice" => $this->id,
                ":groupe" => $this->groupe->getId())) ;
            }

            $this->groupe->save() ;
        }
    }

    private function registerToEducatrices()
    {
        $educatrices = Educatrices::getInstance() ;
        $educatrices->register($this) ;
    }
}