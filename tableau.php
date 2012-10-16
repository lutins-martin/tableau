<?php
include_once("includes/startup.php") ;
ob_start() ;

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

$tableau_js_stat = stat("js/tableau.js") ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="all" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/text.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/1248_16_10_10.css" />

    <title>indiquer où sont les moussaillons?</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script src="js/jquery-1.8.2.js" type="text/javascript"></script>
    <script src="js/tableau.js?r=<?=$tableau_js_stat['mtime']?>" type="text/javascript"></script>
    <script type="text/javascript">
<!--
$(document).ready(function()
{
    $("select").change(function(eventObject)
    {
        var selectField = eventObject.currentTarget ;
        jQuery.ajax("moteur.php?deplacement=local&"+selectField.name+"="+selectField.value) ;
    }) ;
}) ;
-->
</script>
</head>
<body>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header">Dites moi, où sont les moussaillons?</h1>
    </div>
</div>
<?php
$listeEducatrices = $lesEducatrices->getLesEducatrices() ;
if(count($listeEducatrices))
{
    foreach($listeEducatrices as $educatrice)
    {
        $firePHP->log($educatrice,'educatrice') ;
?>
<form>
<div class="row">
		<div class="column framedInBlack grid_9">
			<div id="educatrice<?=$educatrice->getId()?>"
				class="column grid_4 framedInRed">
				<?="{$educatrice->getNom()} ({$educatrice->getGroupe()->getNom()})"?>
			</div>
			<div id="changementDeLocal<?=$educatrice->getId()?>"
				class="column grid_4 framedInBlue">
<?
    $selectLocal = new SelectNode(array("name" => "local[{$educatrice->getId()}]",
    "width" => 60));
    $selectLocal->addOption("") ;
    foreach($lesLocaux->getLesLocaux() as $local)
    {
        $selected =($educatrice->getLocal()->getId()==$local->getId()) ;
        $selectLocal->addOption($local->getNom(),$local->getId(),
        $selected) ;
    }
    print $selectLocal->display() ;

?>
				</div>
		</div>
</div>
</form>
<?php
        }
    }
include("pieddepage.php") ;
?>
</body>
</html>