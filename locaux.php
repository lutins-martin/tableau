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

$lesLocaux = Locaux::getInstance() ;
$listeLocaux = $lesLocaux->getLesLocaux() ;
$firePHP->log($_REQUEST,'param') ;
$locauxAChanger=(isset($_REQUEST['item'])?$_REQUEST['item']:null) ;

$processed = false ;

if (is_array($locauxAChanger))
{
    foreach($locauxAChanger as $localId => $newLocal)
    {
        if(isset($newLocal['efface']))
        {
            $local = $lesLocaux->getUnLocal($localId) ;
            if($local->isLoaded()) $local->delete() ;

            $processed = true ;
            unset($locauxAChanger[$localId]) ;
            continue ;
        }
        if(isset($newLocal['nom']))
        {
            if(!is_null($newLocal['nom']) && trim($newLocal['nom'])!="")
            {
                $local = $lesLocaux->getUnLocal($localId) ;
                if($newLocal['nom']!=$local->getNom())
                {
                    $local->setNom($newLocal['nom']) ;
                    $local->save() ;
                    $processed = true ;
                }
            }
        }
    }

}

$locauxAAjouter=(isset($_REQUEST['itemNouveau'])?$_REQUEST['itemNouveau']:null) ;
if(is_array($locauxAAjouter))
{
    foreach($locauxAAjouter as $nouveauLocal)
    {
        if(isset($nouveauLocal['nom']))
        {
            if(!is_null($nouveauLocal['nom']) && trim($nouveauLocal['nom'])!="")
            {
                $local = new Local();
                $local->setNom($nouveauLocal['nom']) ;
                $local->save() ;
                $processed = true ;
            }
        }
    }
}
if ($processed)
{
    header("Location: locaux.php") ;
    exit ;
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
    <title>Ajouter/changer les locaux</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header">Ajouter/changer les locaux</h1>
    </div>
</div>
<form action="locaux.php" method="post">
<div class="row">
    <div class="column grid_3 framedInRed">cliquez pour effacer</div>
    <div class="column grid_4 framedInBlue">nom des locaux</div>
</div>
<?php
if(count($listeLocaux))
{
    foreach($listeLocaux as $local)
    {
?>
<div class="row">
			<div id="localEfface<?=$local->getId()?>"
				class="column grid_3 framedInRed">
<?
        $effaceBox = new CheckBoxNode(array("name" => "item[{$local->getId()}][efface]")) ;
        print $effaceBox->display() ;
?>
			</div>
		    <div id="localNom<?=$local->getId()?>"
		       class="column grid_4 framedInBlue">
<?php
        $boiteDuNom = new TextBoxNode(array("name" => "item[{$local->getId()}][nom]",
        "value" => $local->getNom())) ;
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
    Nouveau local:
    </div>
    <div class="column grid_4">
<?php
    $nouvelleBoiteDuNom = new TextBoxNode(array("name" => "itemNouveau[][nom]")) ;
    print $nouvelleBoiteDuNom->display() ;
    $plus = new ButtonNode(array("name" => "ajoute","value" => "+","title" => "cliquer ici pour ajouter un autre local")) ;
    print $plus->display() ;
?>
    </div>
</div>
<div class="row">
    <div class="column grid_9">
        <input type="submit" value="sauver les changements" disabled/>
        <input type="reset" value="remettre le formulaire à zéro"/>
    </div>
</div>
</form>
<?
include("pieddepage.php") ;
?>

</body>
</html>