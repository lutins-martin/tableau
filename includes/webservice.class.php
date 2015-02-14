<?php

class WebService {

    protected $db;

    protected static $instance;

    protected function __construct() {
        $this->init ();
    }

    public function init() {
        try {
            // create or open the database
            $this->db = Database::getInstance ();
            $firePHP = FirePHP::getInstance ( true );
            $firePHP->setEnabled ( true );
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }

    public static function getInstance() {
        if (! isset ( self::$instance )) self::$instance = new self ();
        return self::$instance;
    }

    public function getCookie($cookie) {
        if (isset ( $_COOKIE [$cookie] ))
            return $_COOKIE [$cookie];
        else
            return null;
    }

    public function getRequestParameter($parametre, $valeurParDefault = null) {
        FirePHP::getInstance ( true )->setEnabled ( true );
        if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents ( 'php://input' );
            if (strlen ( $json ) != 0) {
                FirePHP::getInstance ()->log ( $json, 'input' );
                $variables = json_decode ( $json, true );
                FirePHP::getInstance ()->log ( $variables, 'POST Variables' );
            } else {
                $variables = $_REQUEST ;
            }
        } else {
            $variables = $_REQUEST;
        }
        if (isset ( $variables [$parametre] ))
            return $variables [$parametre];
        else
            return $valeurParDefault;
    }
}