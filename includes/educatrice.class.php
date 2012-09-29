<?php
class Educatrice extends Element
{

    const SELECT_PAR_NOM = "select NOM,ROWID from EDUCATRICES where NOM = :nom" ;
    const SELECT_PAR_ROWID = "select NOM,ROWID from EDUCATRICES where ROWID= :rowid" ;

    const INSERT = "insert into EDUCATRICES (NOM) values (:nom) returning ROWID" ;
    const UPDATE = "update EDUCATRICES set NOM=:nom where ROWID=:rowid returning ROWID" ;
    const DELETE = "delete from EDUCATRICES where ROWID=:rowid" ;

    public function __construct($nomOrId=null)
    {
        parent::__construct($nomOrId,self::SELECT_PAR_NOM,
        self::SELECT_PAR_ROWID,self::INSERT,self::UPDATE,self::DELETE) ;
    }
}