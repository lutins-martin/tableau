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


$lesMessages = Messages::getInstance() ;
$listeMessages = $lesMessages->getLesMessages() ;

$firePHP->log($_REQUEST,'param') ;

$messageId = (isset($_REQUEST['message'])?$_REQUEST['message']:null) ;

$message = $lesMessages->getUnMessage($messageId) ;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="all" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/text.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/1248_16_10_10.css" />
    <script type="text/javascript" src="js/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="js/messages.js"></script>
    <title>Ajouter/changer un message</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header">Ajouter/changer un message</h1>
    </div>
</div>
<form action="messages.php" method="post">
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_4 framedInRed">début</div>
    <div class="column grid_4 framedInRed">fin</div>
</div>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_4 framedInRed"><input type="text" name="debut" value="<?=$message->getDebut(true)?>"></input></div>
    <div class="column grid_4 framedInRed"><input type="text" name="fin" value="<?=$message->getFin(true)?>"></div>
</div>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_13 framedInBlue"><label for="titreMessage">Titre </label>
    <input id="titreMessage" type="text" name="titre" value="<?=$message->getTitre()?>" size="100"></input></div>
</div>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_13 framedInBlue"><textarea name="message" cols="120" rows="30"><?=$message->getMessage()?></textarea></div>
</div>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_8 framedInRed"><input type="reset" name="reset" value="annuler les modifications"></input>&nbsp;
    <input type="submit" name="submit" value="sauvegarder les changements"></input></div>
</div>
</form>
<?
include("pieddepage.php") ;
?>

</body>
</html>