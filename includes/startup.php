<?php
$tableauRootDir=$_SERVER['DOCUMENT_ROOT'] ;
set_include_path(get_include_path().":$tableauRootDir/tableau/includes:$tableauRootDir/tableau/firephp:$tableauRootDir/tableau/htmlnodes") ;
spl_autoload_extensions(".class.php") ;
spl_autoload_register() ;

ob_start() ;
?>