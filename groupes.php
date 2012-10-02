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

$lesGroupes = Groupes::getInstance() ;
$listeGroupes = $lesGroupes->getLesGroupes() ;
$firePHP->log($_REQUEST,'param') ;
$groupesAChanger=(isset($_REQUEST['item'])?$_REQUEST['item']:null) ;


if (is_array($groupesAChanger))
{
    $processed = false ;

    foreach($groupesAChanger as $groupeId => $newgroupe)
    {
        if(isset($newgroupe['efface']))
        {
            $groupe = $lesGroupes->getUnGroupe($groupeId) ;
            if($groupe->isLoaded()) $groupe->delete() ;

            $processed = true ;
            unset($groupesAChanger[$groupeId]) ;
            continue ;
        }
        if(isset($newgroupe['nom']))
        {
            if(!is_null($newgroupe['nom']) && trim($newgroupe['nom'])!="")
            {
                $groupe = $lesGroupes->getUnGroupe($groupeId) ;
                $firePHP->log($groupe,'object groupe') ;
                if($newgroupe['nom']!=$groupe->getNom())
                {
                    $groupe->setNom($newgroupe['nom']) ;
                    $groupe->save() ;
                    $processed = true ;
                    $firePHP->log($groupe,'object groupe') ;
                }
            }
        }
    }
    if ($processed)
    {
        header("Location: groupes.php") ;
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
    <title>Ajouter/changer les groupes</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header">Ajouter/changer les groupes</h1>
    </div>
</div>
<form action="groupes.php" method="post">
<div class="row">
    <div class="column grid_3 framedInRed">cliquez pour effacer</div>
    <div class="column grid_4 framedInBlue">nom des groupes</div>
</div>
<?php
if(count($listeGroupes))
{
    foreach($listeGroupes as $groupe)
    {
?>
<div class="row">
			<div id="groupeEfface<?=$groupe->getId()?>"
				class="column grid_3 framedInRed">
<?
        $effaceBox = new CheckBoxNode(array("name" => "item[{$groupe->getId()}][efface]")) ;
        print $effaceBox->display() ;
?>
			</div>
		    <div id="groupeNom<?=$groupe->getId()?>"
		       class="column grid_4 framedInBlue">
<?php
        $boiteDuNom = new TextBoxNode(array("name" => "item[{$groupe->getId()}][nom]",
        "value" => $groupe->getNom())) ;
        print $boiteDuNom->display() ;
?>
		       </div>
</div>
<?php
        }
    }
?>
<div class="row">
    <div class="column grid_3">
    Nouveau groupe:
    </div>
    <div class="column grid_4">
<?php
    $nouvelleBoiteDuNom = new TextBoxNode(array("name" => "item[][nom]")) ;
    print $nouvelleBoiteDuNom->display() ;
    $plus = new ButtonNode(array("name" => "ajoute","value" => "+","title" => "cliquer ici pour ajouter un autre groupe")) ;
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