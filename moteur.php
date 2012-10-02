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
$deplacement = (isset($_REQUEST['deplacement'])?$_REQUEST['deplacement']:"relecture") ;
switch ($deplacement)
{
    case 'local' :
        try
        {
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
            print json_encode($output['resultat']=true) ;
        }
        catch (Exception $e)
        {
            $output['resultat'] = false ;
            $output['erreur'] = $e->getMessage() ;
            print json_encode($output) ;
        }
        break;
    case 'relecture' :
        header("Content-type: application/json") ;
        $tableau = array() ;
        $listeEducatrices = $lesEducatrices->getLesEducatrices() ;
        foreach($listeEducatrices  as $educatrice)
        {
            $edu['nom'] = $educatrice->getNom() ;
            $edu['groupe']['nom'] = $educatrice->getGroupe()->getNom() ;
            $edu['groupe']['id'] = $educatrice->getGroupe()->getId() ;
            $edu['local']['nom'] = $educatrice->getGroupe()->getLocal()->getNom() ;
            $edu['local']['id'] = $educatrice->getGroupe()->getLocal()->getId() ;
            $tableau['locaux'][$educatrice->getId()] = $edu ;
        }
        print json_encode($tableau) ;
        break;
    default:
        print json_encode($output['resultat']=true) ;
        break ;

}

?>