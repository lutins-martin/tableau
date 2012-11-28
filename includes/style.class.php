<?php
class Style extends Element
{

    private $nomDuFichierCss ;
    private $actif ;

    const SELECT_PAR_NOM = "select NOM,FICHIER,ACTIF,ROWID from STYLES where NOM = :nom" ;
    const SELECT_PAR_ROWID = "select NOM,FICHIER,ACTIF,ROWID from STYLES where ROWID= :rowid" ;

    const INSERT = "insert into STYLES (NOM,FICHIER,ACTIF) values (:nom,:fichier,:actif)" ;
    const UPDATE = "update STYLES set NOM=:nom,FICHIER=:fichier,ACTIF=:actif where ROWID=:rowid and ((NOM!=:nom)
    or (FICHIER!=:fichier) or (ACTIF!=:actif))" ;
    const DELETE = "delete from STYLES where ROWID=:rowid" ;

    public function __construct($nomOrId=null)
    {
        parent::__construct($nomOrId,self::SELECT_PAR_NOM,
        self::SELECT_PAR_ROWID,self::INSERT,self::UPDATE,self::DELETE) ;
    }

    public function save()
    {
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