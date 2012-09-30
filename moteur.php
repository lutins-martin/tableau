<?php
include_once("includes/startup.php") ;
try
{
  //create or open the database
  $database = Database::getInstance() ;
  $firePHP = FirePHP::getInstance(true) ;
  $firePHP->setEnabled(true) ;
}
catch(Exception $e)
{
  die($error);
}

$lesGroupes=Groupes::getInstance() ;
$lesLocaux=Locaux::getInstance() ;
$lesEducatrices=Educatrices::getInstance() ;

$firePHP = FirePHP::getInstance() ;
switch ($_REQUEST['deplacement'])
{
    case 'local' :
        $listeEducatrice=$_REQUEST['local'] ;
        $firePHP->log($listeEducatrice,'educatrice') ;
        if(is_array($listeEducatrice))
        {
            $firePHP->trace(__FILE__.":".__LINE__) ;
            foreach($listeEducatrice as $educatriceId => $localId)
            {
                $educatrice = $lesEducatrices->getUneEducatrice($educatriceId) ;
                $firePHP->log($educatrice,'educatrice object') ;
                if($educatrice->isLoaded())
                {
                    $educatrice->setLocal($localId) ;
                    $educatrice->save() ;
                }
            }
        }
}

?>