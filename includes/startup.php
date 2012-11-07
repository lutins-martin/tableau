<?php
$tableauRootDir=$_SERVER['DOCUMENT_ROOT'] ;
set_include_path(get_include_path().":$tableauRootDir/tableau/includes:$tableauRootDir/tableau/js:$tableauRootDir/tableau/css:$tableauRootDir/tableau/firephp:$tableauRootDir/tableau/htmlnodes:$tableauRootDir/tableau/jquery-ui-1.9.0.custom/js") ;
spl_autoload_extensions(".class.php") ;
spl_autoload_register() ;
$scriptName = basename( $_SERVER['SCRIPT_FILENAME']) ;

$firePHP = FirePHP::getInstance(true) ;
$firePHP->log($scriptName,'scriptName') ;
switch($scriptName)
{
    case "index.php" : $page = PageTableau::getInstance() ;
    break ;
    case "deplacements.php" : $page = PageDeplacements::getInstance() ;
    break ;
    case "locaux.php" : $page = PageLocaux::getInstance() ;
    break ;
    case "groupes.php" : $page = PageGroupes::getInstance() ;
    break ;
    case "educatrices.php" : $page = PageEducatrices::getInstance() ;
    break ;
    case "messages.php" : $page = PageMessages::getInstance() ;
    break ;
    case "modmessage.php" : $page = PageModMessage::getInstance() ;
    break ;
    case "moteur.php" : $page = Moteur::getInstance() ;
    break ;
}

ob_start() ;
?>