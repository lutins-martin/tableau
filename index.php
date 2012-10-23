<?php
//phpinfo() ;
ini_set("display_errors","on") ;
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
  die($e->getMessage());
}


$lesEducatrices=Educatrices::getInstance() ;
$tableau_js_stat = stat("js/tableau.js") ;

$lesMessages = Messages::getInstance() ;
$lesMessageAujourdhui = $lesMessages->getLesMessages(true) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="all" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/text.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/1248_16_10_10.css" />

    <title>Où sont les moussaillons?</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript" src="js/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="js/tableau.js?r=<?=$tableau_js_stat['mtime']?>">
    </script>

</head>
<body>
<div id="dateheure" class="row">
    <div class="column grid_12">
        <h1 id="date" class="date"></h1>
    </div>
    <div class="column grid_4"><h1 id="heure" class="heure"></h1></div>
</div>
<div id="grille" class="column grid_9">
<?php
$listeEducatrices = $lesEducatrices->getLesEducatrices() ;
if(count($listeEducatrices))
{
    foreach($listeEducatrices as $educatrice)
    {
        $firePHP->log($educatrice,'educatrice') ;
?>
<div class="column grid_9">
<div class="row">
		<div class="column framedInBlack grid_8">
			<div id="educatrice<?=$educatrice->getId()?>"
				class="column grid_4 framedInRed">
				<?="{$educatrice->getNom()} ({$educatrice->getGroupe()->getNom()})"?>
			</div>
			<div id="local<?=$educatrice->getId()?>"
				class="column grid_3 framedInBlue"><?="{$educatrice->getGroupe()->getLocal()->getNom()}"?></div>
		</div>
	</div>
</div>
<?php
    }
}
?>
</div>
<div class="column grid_7">
<?php
foreach($lesMessageAujourdhui as $message)
{
    ?>
    <div id="message<?=$message->getId()?>" class="row">
    <div class="column grid_6 message">
    <h1><?=$message->getTitre()?></h1>
    <div><?=$message->getMessage()?></div>
    </div>
    </div>
    <?
}
?>
</div>
<?
include("pieddepage.php") ;
?>
</body>
</html>
