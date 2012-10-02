<?php
//phpinfo() ;
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

$lesEducatrices = Educatrices::getInstance() ;
$lesGroupes = Groupes::getInstance() ;
$tousLesGroupes = $lesGroupes->getLesGroupes();
$listeEducatrices = $lesEducatrices->getLesEducatrices() ;
$firePHP->log($_REQUEST,'param') ;
$educatricesAChanger=(isset($_REQUEST['item'])?$_REQUEST['item']:null) ;


if (is_array($educatricesAChanger))
{
    $processed = false ;

    foreach($educatricesAChanger as $educatriceId => $neweducatrice)
    {
        if(isset($neweducatrice['efface']))
        {
            $educatrice = $lesEducatrices->getUneEducatrice($educatriceId) ;
            if($educatrice->isLoaded()) $educatrice->delete() ;

            $processed = true ;
            unset($educatricesAChanger[$educatriceId]) ;
            continue ;
        }

        $educatrice = $lesEducatrices->getUneEducatrice($educatriceId) ;
        if(isset($neweducatrice['nom']))
        {
            $firePHP->log($neweducatrice,'neweducatrice') ;
            if(!is_null($neweducatrice['nom']) && trim($neweducatrice['nom'])!="")
            {
                if($neweducatrice['nom']!=$educatrice->getNom())
                {
                    $educatrice->setNom($neweducatrice['nom']) ;
                    $educatrice->save() ;
                    $processed = true ;
                    $firePHP->log($educatrice,'object educatrice') ;
                }
            }
        }
        if(isset($neweducatrice['groupe']))
        {
            if($neweducatrice['groupe']!=$educatrice->getGroupe()->getId())
            {
                $educatrice->setGroupe($lesGroupes->getUnGroupe($neweducatrice['groupe'])) ;
                $educatrice->save() ;
                $processed = true ;
            }
        }
    }
    if ($processed)
    {
        header("Location: educatrices.php") ;
        exit ;
    }

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="all" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/text.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/1248_16_10_10.css" />
    <script type="text/javascript" src="js/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="js/ajoute.js"></script>
    <title>Ajouter/changer les educatrices</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header">Ajouter/changer les educatrices</h1>
    </div>
</div>
<form action="educatrices.php" method="post">
<div class="row">
    <div class="column grid_3 framedInRed">cliquez pour effacer</div>
    <div class="column grid_4 framedInBlue">nom des educatrices</div>
    <div class="column grid_4 framedInBlue">groupe</div>
</div>
<?php
if(count($listeEducatrices))
{
    foreach($listeEducatrices as $educatrice)
    {
?>
<div class="row">
			<div id="educatriceEfface<?=$educatrice->getId()?>"
				class="column grid_3 framedInRed">
<?
        $effaceBox = new CheckBoxNode(array("name" => "item[{$educatrice->getId()}][efface]")) ;
        print $effaceBox->display() ;
?>
			</div>
		    <div id="educatriceNom<?=$educatrice->getId()?>"
		       class="column grid_4 framedInBlue">
<?php
        $boiteDuNom = new TextBoxNode(array("name" => "item[{$educatrice->getId()}][nom]",
        "value" => $educatrice->getNom())) ;
        print $boiteDuNom->display() ;
?>
		       </div>
		       <div id="educatriceGroupe<?=$educatrice->getId()?>" class="column grid_4 framedInBlue">
<?php
        $selectDeGroupe = new SelectNode(array("name" => "item[{$educatrice->getId()}][groupe]")) ;
        $selectDeGroupe->addOption("",0) ;
        foreach($tousLesGroupes as $groupe)
        {
            $firePHP->log($groupe,"groupe dans create select") ;
            $selectDeGroupe->addOption($groupe->getNom(),$groupe->getId(),($groupe->getId()==$educatrice->getGroupe()->getId())) ;
        }
        print $selectDeGroupe->display() ;
?>
		       </div>
</div>
<?php
        }
    }
?>
<div class="row">
    <div class="column grid_3">
    Nouvelle educatrice(teur):
    </div>
    <div class="column grid_4">
<?php
    $nouvelleBoiteDuNom = new TextBoxNode(array("name" => "item[][nom]")) ;
    print $nouvelleBoiteDuNom->display() ;
    $plus = new ButtonNode(array("name" => "ajoute","value" => "+","title" => "cliquer ici pour ajouter une autre �ducatrice(teur)")) ;
    print $plus->display() ;
?>
    </div>
</div>
<div class="row">
    <div class="column grid_9">
        <input type="submit" value="appliquer les changements" disabled/>
        <input type="reset" value="remettre le formulaire à zéro"/>
    </div>
</div>
</form>
<?
include("pieddepage.php") ;
?>

</body>
</html>