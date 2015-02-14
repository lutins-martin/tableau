<?php

class Moteur extends WebService {

    protected $lesEducatrices;

    protected $lesLocaux;

    protected $lesMessages;

    protected $lesStyles;

    const ACTION_TABLEAU_CHANGER_LOCAL = 'changerLocal';

    const ACTION_TABLEAU_RELECTURE = 'relecture';

    const ACTION_MESSAGES_RELECTURE = 'relectureMessages';

    const ACTION_SAUVE_MESSAGE = 'sauveMessage';

    const ACTION_EFFACE_MESSAGES = 'effaceMessages';

    const ACTION_TABLEAU_TOUS_LES_LOCAUX_UNE_EDUCATRICE = 'tousLesLocauxPour';

    const ACTION_TABLEAU_TOUS_LES_LOCAUX = 'tousLesLocaux';

    const ACTION_TABLEAU_TOUTES_LES_EDUCATRICES_AVEC_GROUPES = 'toutesLesEducatricesAvecGroupes';

    const ACTION_TABLEAU_TOUS_LES_GROUPES = 'tousLesGroupes';

    const ACTION_TABLEAU_BACKGROUND_IMAGE = "getBackgroundImage";
    
    const ACTION_GET_BACKGROUND_FILENAME = "getBackgroundImageFileName" ;

    const ACTION_TOUS_LES_BACKGROUNDS = "tousLesBackgrounds";

    const ACTION_EFFACER_BACKGROUND_FILES = "effacerBackgroundFiles";

    const ACTION_CHANGE_BACKGROUND = "changeBackground";

    const ACTION_UPLOAD_BACKGROUND = "uploadBackground";

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
        FirePHP::getInstance ( true )->setEnabled ( true );
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
                        $educatriceSimple ['groupe'] ['nom'] = $educatrice->getGroupe ()->getNom ();
                        $educatriceSimple ['groupe'] ['valeur'] = $educatrice->getGroupe ()->getId ();
                        
                        $output ['educatrices'] [] = $educatriceSimple;
                    }
                    print json_encode ( $output );
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
                $tousLesMessages = $this->getRequestParameter ( 'tous', true );
                foreach ( $this->lesMessages->getLesMessages ( $tousLesMessages ) as $key => $message ) {
                    $msg ['id'] = $message->getId ();
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
            case self::ACTION_SAUVE_MESSAGE :
                $output = array ();
                $editedMessage = $this->getRequestParameter ( 'editedMessage' );
                if ($editedMessage) {
                    if (isset ( $editedMessage ['id'] )) {
                        $message = $this->lesMessages->getUnMessage ( $editedMessage ['id'] );
                    } else {
                        $message = new Message ();
                    }
                    FirePHP::getInstance ( true )->setEnabled ( true );
                    FirePHP::getInstance ()->log ( $message, 'Message avant' );
                    $message->setDebut ( $editedMessage ['debut'] );
                    $message->setFin ( $editedMessage ['fin'] );
                    $message->setTitre ( $editedMessage ['titre'] );
                    $message->setMessage ( $editedMessage ['corps'] );
                    FirePHP::getInstance ()->log ( $message, "message après" );
                    $message->save ();
                    
                    $output ['success'] = true;
                    print json_encode ( $output );
                }
                break;
            case self::ACTION_EFFACE_MESSAGES :
                $output = array ();
                $messagesToDelete = $this->getRequestParameter ( 'messageAEffacer' );
                foreach ( $messagesToDelete as $messageId ) {
                    $message = $this->lesMessages->getUnMessage ( $messageId );
                    if ($message->isLoaded ()) {
                        $message->delete ();
                    }
                }
                $output ['success'] = true;
                print json_encode ( $output );
                break;
            case self::ACTION_TABLEAU_BACKGROUND_IMAGE :
                $backgroundImageFileName = "css/" . Styles::getInstance ()->getFichierStyleActif ();
                $size = getimagesize ( $backgroundImageFileName );
                
                // Now that you know the mime type, include it in the header.
                header ( 'Content-type: ' . $size ['mime'] );
                
                // Read the image and send it directly to the output.
                readfile ( $backgroundImageFileName );
                break;
            case self::ACTION_GET_BACKGROUND_FILENAME :
                $output = array() ;
                $output['file'] = $backgroundImageFileName = "css/" . Styles::getInstance ()->getFichierStyleActif ();
                print json_encode($output) ;
                break ;
            case self::ACTION_TOUS_LES_BACKGROUNDS :
                FirePHP::getInstance ( true )->setEnabled ( true );
                FirePHP::getInstance ()->trace ( __METHOD__ );
                $subdir = dirname ( $_SERVER ['REQUEST_URI'] );
                $backgroundPath = $_SERVER ['DOCUMENT_ROOT'] . "$subdir/css/styles/";
                $backgroundThumbnailPath = $_SERVER ['DOCUMENT_ROOT'] . "$subdir/thumbnails/";
                FirePHP::getInstance ()->log ( $backgroundPath, 'BackgroundPath' );
                FirePHP::getInstance ()->log ( $backgroundThumbnailPath, 'BackgroundPathThumbnail' );
                if (! file_exists ( $backgroundThumbnailPath )) {
                    mkdir ( $backgroundThumbnailPath );
                }
                $backgrounds = glob ( $backgroundPath . "*.jpg" );
                FirePHP::getInstance ()->trace ( __METHOD__ );
                FirePHP::getInstance ()->log ( $backgrounds, 'backgrounds' );
                $activeImage = basename ( Styles::getInstance ()->getFichierStyleActif () );
                FirePHP::getInstance ()->log ( $activeImage, 'Active Image Name' );
                $output = array ();
                foreach ( $backgrounds as $background ) {
                    $bg = array ();
                    if (! file_exists ( $backgroundThumbnailPath . basename ( $background ) )) {
                        $convertCommand = "/usr/bin/convert -resize 150 '$background' '$backgroundThumbnailPath" . basename ( 
                                $background ) . "' 2>&1";
                        $returnValue = null;
                        $conversion = system ( $convertCommand, $returnValue );
                        FirePHP::getInstance ()->log ( $conversion, "Conversion => $returnValue =>" . $convertCommand );
                    }
                    $bg ['file'] = "$subdir/css/styles/" . str_replace ( " ", "%20", basename ( $background ) );
                    $bg ['thumb'] = "$subdir/thumbnails/" . basename ( $background );
                    $bg ['nom'] = preg_replace ( "/.jpg$/", "", basename ( $background ) );
                    FirePHP::getInstance ()->log ( basename ( $background ), 'Image Name' );
                    if (basename ( $background ) == $activeImage) {
                        $bg ['active'] = true;
                    }
                    $output ['backgrounds'] [] = $bg;
                }
                print json_encode ( $output );
                break;
            case self::ACTION_EFFACER_BACKGROUND_FILES :
                FirePHP::getInstance ( true )->setEnabled ( true );
                $output = array ();
                $subdir = dirname ( $_SERVER ['REQUEST_URI'] );
                $backgroundPath = $_SERVER ['DOCUMENT_ROOT'] . "$subdir/css/styles/";
                $backgroundThumbnailPath = $_SERVER ['DOCUMENT_ROOT'] . "$subdir/thumbnails/";
                $output ['success'] = false;
                $files = $this->getRequestParameter ( 'files' );
                foreach ( $files as $file ) {
                    FirePHP::getInstance ()->log ( $backgroundPath . basename ( str_replace ( "%20", " ", $file ['file'] ) ), 
                            "unlink file" );
                    FirePHP::getInstance ()->log ( $backgroundThumbnailPath . basename ( $file ['thumb'] ), "unlink thumb" );
                    unlink ( $backgroundPath . basename ( str_replace ( "%20", " ", $file ['file'] ) ) );
                    unlink ( $backgroundThumbnailPath . basename ( $file ['thumb'] ) );
                }
                print json_encode ( $output );
                break;
            case self::ACTION_CHANGE_BACKGROUND :
                $nouveauBackground = $this->getRequestParameter ( 'background' );
                Styles::getInstance ()->setActif ( str_replace ( "%20", " ", basename ( $nouveauBackground ['file'] ) ) );
                $output ['actif'] = Styles::getInstance ()->getFichierStyleActif ();
                $output ['success'] = true;
                print json_encode ( $output );
                break;
            case self::ACTION_UPLOAD_BACKGROUND :
                FirePHP::getInstance ()->log ( $_FILES, "_FILES" );
                $subdir = dirname ( $_SERVER ['REQUEST_URI'] );
                $backgroundPath = $_SERVER ['DOCUMENT_ROOT'] . "$subdir/css/styles/";
                $backgroundThumbnailPath = $_SERVER ['DOCUMENT_ROOT'] . "$subdir/thumbnails/";
                ini_set ( "file_uploads", 1 );
                ini_set ( "upload_max_filesize", "3M" );
                /* ini_set ( "upload_tmp_dir", $tempdir ); no effect */
                
                $target_file = basename ( $_FILES ["backgroundFile"] ["name"] );
                $uploadOk = 1;
                // Check if image file is a actual image or fake image

                $check = getimagesize ( $_FILES ["backgroundFile"] ["tmp_name"] );
                if ($check !== false) {                    
                    $uploadOk = 1;
                } else {
                    throw new Exception("Le fichier n'est pas une image");
                }
                if($uploadOk) {
                    $convertCommand = "/usr/bin/convert -resize 1366 '" . $_FILES['backgroundFile']['tmp_name'] . "' '$backgroundPath" . basename (
                            $_FILES['backgroundFile'] ['name'] ) . "' 2>&1";
                    system($convertCommand) ;
                    $convertThumbCommand = "/usr/bin/convert -resize 150 '" . $_FILES['backgroundFile']['tmp_name'] . "' '$backgroundThumbnailPath" . basename (
                            $_FILES['backgroundFile'] ['name'] ) . "' 2>&1";
                    system($convertCommand) ;
                    $returnValue = null;
                }
                header("location: $subdir/#/arriereplans/") ;
                exit ;                
                
                break;
            default :
                print json_encode ( $output ['resultat'] = true );
                break;
        }
    }
} 