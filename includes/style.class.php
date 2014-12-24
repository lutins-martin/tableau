<?php
class Style extends Element
{

    private $nomDuFichierCss ;
    private $actif ;

    const SELECT_PAR_NOM = "select NOM,FICHIER,ACTIF,ROWID from STYLES where NOM = :nom" ;
    const SELECT_PAR_ROWID = "select NOM,FICHIER,ACTIF,ROWID from STYLES where ROWID= :rowid" ;

    const INSERT = "insert into STYLES (NOM,FICHIER,ACTIF) values (:nom,:fichier,:actif)" ;
    const UPDATE = "update STYLES set NOM=:nom,FICHIER=:fichier,ACTIF=:actif where ROWID=:rowid and ((NOM!=:nom)
    or (FICHIER!=:fichier) or (FICHIER is null))" ;
    const DELETE = "delete from STYLES where ROWID=:rowid" ;

    public function __construct($nomOrId=null)
    {
        parent::__construct($nomOrId,self::SELECT_PAR_NOM,
        self::SELECT_PAR_ROWID,self::INSERT,self::UPDATE,self::DELETE) ;
        $this->actif = false ;
    }

    public function setNomDeFichier($nomDeFichier)
    {
        $this->nomDuFichierCss = $nomDeFichier ;
    }

    public function loadFromDatabaseRecord($record)
    {
        $firePHP = FirePHP::getInstance() ;

        $firePHP->log($record,'record') ;
        if (isset($record['NOM']))
        {
            $this->nom = $record['NOM'] ;
            $this->id = $record['rowid'] ;
            $this->nomDuFichierCss = $record['FICHIER'] ;
            $this->actif = ($record['ACTIF']==1)?true:false;
            $this->loaded = true ;
        }
    }


    public function getNomDeFichier()
    {
        return $this->nomDuFichierCss ;
    }

    public function getActif()
    {
        return $this->actif ;
    }

    public function save()
    {
        $firePHP= FirePHP::getInstance() ;
        $firePHP->trace(__METHOD__) ;
        $values[":fichier"] = $this->nomDuFichierCss ;
        $values[":actif"] = $this->actif ;
        parent::save($values) ;
    }

    private function registerToSTYLES()
    {
        $STYLES = STYLES::getInstance() ;
        $STYLES->register($this) ;
    }

}