<?php

class Moteur extends WebService {

    protected $lesEducatrices;

    protected $lesLocaux;

    protected $lesMessages;

    protected $lesStyles;

    const ACTION_TABLEAU_CHANGER_LOCAL = 'changerLocal';

    const ACTION_TABLEAU_RELECTURE = 'relecture';

    const ACTION_MESSAGES_RELECTURE = 'relectureMessages';

    const ACTION_TABLEAU_TOUS_LES_LOCAUX_UNE_EDUCATRICE = 'tousLesLocauxPour';

    const ACTION_TABLEAU_TOUS_LES_LOCAUX = 'tousLesLocaux';

    const ACTION_TABLEAU_TOUTES_LES_EDUCATRICES_AVEC_GROUPES = 'toutesLesEducatricesAvecGroupes';

    const ACTION_TABLEAU_TOUS_LES_GROUPES = 'tousLesGroupes';

    const ACTION_TABLEAU_BACKGROUND_IMAGE = "getBackgroundImage";

    public static function getInstance() {
        if (! isset ( self::$instance )) self::$instance = new self ();
        return self::$instance;
    }

    public function init() {
        parent::init ();
        $this->lesEducatrices = Educatrices::getInstance ();
        $this->lesLocaux = Locaux::getInstance ();
        $this->lesMessages = Messages::getInstance ();
        header ( "Content-type: application/json" );
        header ( "Content: text/html; charset=UTF-8" );
    }

    public function processRequest() {
        $firePHP = FirePHP::getInstance ();
        
        $action = $this->getRequestParameter ( 'action' );
        
        switch ($action) {
            case self::ACTION_TABLEAU_CHANGER_LOCAL :
                try {
                    $listeEducatrice = $this->getRequestParameter ( 'local' );
                    $firePHP->log ( $listeEducatrice, 'educatrice' );
                    if (is_array ( $listeEducatrice )) {
                        $firePHP->trace ( __FILE__ . ":" . __LINE__ );
                        foreach ( $listeEducatrice as $educatriceId => $localId ) {
                            $educatrice = $this->lesEducatrices->getUneEducatrice ( $educatriceId );
                            $firePHP->log ( $educatrice, 'educatrice object' );
                            if ($educatrice->isLoaded ()) {
                                $educatrice->setLocal ( $localId );
                                $educatrice->save ();
                            }
                        }
                    }
                    print json_encode ( $output ['resultat'] = true );
                } catch ( Exception $e ) {
                    $output ['resultat'] = false;
                    $output ['erreur'] = $e->getMessage ();
                    print json_encode ( $output );
                }
                break;
            case self::ACTION_TABLEAU_RELECTURE :
                header ( "Content-type: application/json" );
                $tableau = array ();
                $listeEducatrices = $this->lesEducatrices->getLesEducatrices ();
                $format = $this->getRequestParameter ( "format" );
                foreach ( $listeEducatrices as $educatrice ) {
                    $edu ['nom'] = $educatrice->getNom ();
                    $edu ['groupe'] ['nom'] = $educatrice->getGroupe ()->getNom ();
                    $edu ['groupe'] ['id'] = $educatrice->getGroupe ()->getId ();
                    $edu ['local'] ['nom'] = $educatrice->getGroupe ()->getLocal ()->getNom ();
                    $edu ['local'] ['id'] = $educatrice->getGroupe ()->getLocal ()->getId ();
                    $tableau ['locaux'] [$educatrice->getId ()] = $edu;
                }
                
                $lesMessagesActifs = $this->lesMessages->getLesMessages ( true );
                $dernierChangement = strtotime ( strftime ( "%F" ) );
                foreach ( $lesMessagesActifs as $unMessage ) {
                    $dernierChangement = max ( 
                            array (
                                    $dernierChangement,
                                    $unMessage->getDernierChangement () ) );
                }
                $dernierStyleStm = $this->db->query ( "select HEUREDATE from DERNIERCHANGEMENT" );
                $dernierStyle = strtotime ( $dernierStyleStm->fetch ( PDO::FETCH_COLUMN ) );
                $tableau ['dernierChangement'] = max ( 
                        array (
                                $dernierChangement,
                                $dernierStyle ) );
                if ($format == "asAVariable") {
                    print "var tousLesLocaux = " . json_encode ( $tableau );
                } else {
                    print json_encode ( $tableau );
                }
                
                break;
            case self::ACTION_TABLEAU_TOUS_LES_LOCAUX_UNE_EDUCATRICE :
            case self::ACTION_TABLEAU_TOUS_LES_LOCAUX :
                try {
                    $educatriceId = $this->getRequestParameter ( 'educatriceId' );
                    $educatrice = $this->lesEducatrices->getUneEducatrice ( $educatriceId );
                    $firePHP->log ( $educatrice, 'educatrice object' );
                    if ($educatriceId !== null) {
                        $output ['educatriceId'] = $educatriceId;
                        $output ['selected'] = $educatrice->getLocal ()->getId ();
                    }
                    $lesLocaux = $this->lesLocaux->getLesLocaux ();
                    foreach ( $lesLocaux as $local ) {
                        $localSimple = array ();
                        $localSimple ['name'] = $local->getNom ();
                        $localSimple ['value'] = intval ( $local->getId () );
                        if (isset ( $output ['selected'] ) && ($local->getId () == $output ['selected'])) $localSimple ['selected'] = 'selected';
                        $output ['locaux'] [] = $localSimple;
                    }
                    print json_encode ( $output );
                } catch ( Exception $e ) {
                    $output ['resultat'] = false;
                    $output ['erreur'] = $e->getMessage ();
                    print json_encode ( $output );
                }
                break;
            case self::ACTION_TABLEAU_TOUTES_LES_EDUCATRICES_AVEC_GROUPES :
                try {
                    $output ['educatrices'] = array ();
                    foreach ( $this->lesEducatrices->getLesEducatrices () as $educatrice ) {
                        $educatriceSimple ['nom'] = $educatrice->getNom ();
                        $educatriceSimple ['valeur'] = $educatrice->getId ();
                        $educatriceSimple ['groupe']['nom'] = $educatrice->getGroupe()->getNom() ;
                        $educatriceSimple ['groupe']['valeur'] = $educatrice->getGroupe()->getId() ;
                        
                        $output['educatrices'][] = $educatriceSimple ;
                    }
                    print json_encode($output) ;
                } catch ( Exception $e ) {
                    $output ['resultat'] = false;
                    $output ['error'] = $e->getMessage ();
                    header ( $_SERVER ['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500 );
                    print json_encode ( $output );
                }
                break;
            case self::ACTION_TABLEAU_TOUS_LES_GROUPES :
                try {
                    $output ['groupes'] = array ();
                    foreach ( Groupes::getInstance ()->getLesGroupes () as $groupe ) {
                        $groupeSimple = array ();
                        $groupeSimple ['nom'] = $groupe->getNom ();
                        $groupeSimple ['valeur'] = $groupe->getId ();
                        $output ['groupes'] [] = $groupeSimple;
                    }
                    print json_encode ( $output );
                } catch ( Exception $e ) {
                    $output ['resultat'] = false;
                    $output ['erreur'] = $e->getMessage ();
                    print json_encode ( $output );
                }
                break;
            case self::ACTION_MESSAGES_RELECTURE :
                $output = array ();
                foreach ( $this->lesMessages->getLesMessages ( true ) as $key => $message ) {
                    $msg ['titre'] = $message->getTitre ();
                    $msg ['corps'] = $message->getMessage ();
                    $msg ['debut'] = strftime ( "%F", $message->getDebut () );
                    $msg ['fin'] = strftime ( "%F", $message->getFin () );
                    $output [$key] = $msg;
                }
                FirePHP::getInstance ()->log ( $output, 'les messages' );
                if ($this->getRequestParameter ( 'format' ) == 'asAVariable') {
                    print "tousLesMessages=" . json_encode ( $output );
                } else {
                    print json_encode ( $output );
                }
                break;
            case self::ACTION_TABLEAU_BACKGROUND_IMAGE :
                $backgroundImageFileName = "css/" . Styles::getInstance ()->getFichierStyleActif ();
                $size = getimagesize ( $backgroundImageFileName );
                
                // Now that you know the mime type, include it in the header.
                header ( 'Content-type: ' . $size ['mime'] );
                
                // Read the image and send it directly to the output.
                readfile ( $backgroundImageFileName );
                break;
            default :
                print json_encode ( $output ['resultat'] = true );
                break;
        }
    }
}