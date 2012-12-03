<?php
class WebService
{
    protected $db ;

    protected static $instance ;

    protected function __construct()
    {
        $this->init() ;
    }

    public function init()
    {
        try
        {
            //create or open the database
            $this->db = Database::getInstance() ;
            $firePHP = FirePHP::getInstance(true) ;
            $firePHP->setEnabled(true) ;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }


    }

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    public function getCookie($cookie)
    {
        if(isset($_COOKIE[$cookie])) return $_COOKIE[$cookie] ;
        else return null ;
    }

    public function getRequestParameter($parametre)
    {
        if(isset($_REQUEST[$parametre])) return $_REQUEST[$parametre] ;
        else return null ;
    }
}