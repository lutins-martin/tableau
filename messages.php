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
$messagesAChanger=(isset($_REQUEST['item'])?$_REQUEST['item']:null) ;

$firePHP->log($listeMessages,'Liste messages') ;

if (is_array($messagesAChanger))
{
    $processed = false ;

    foreach($messagesAChanger as $messageID => $newMessage)
    {
        if(isset($newMessage['efface']))
        {
            $message = $lesMessage->getUnMessage($messageID) ;
            if($message->isLoaded()) $message->delete() ;

            $processed = true ;
            unset($messagesAChanger[$messageID]) ;
            continue ;
        }
        if(isset($newMessage['message']))
        {
            if(!is_null($newMessage['message']) && trim($newMessage['message'])!="")
            {
                $message = $lesMessages->getUnMessage($messageID) ;
                $firePHP->log($message,'object message') ;
                if($newMessage['message']!=$message->getMessage())
                {
                    $message->setMessage($newMessage['message']) ;
                    $message->save() ;
                    $processed = true ;
                    $firePHP->log($message,'object message') ;
                }
            }
        }
    }
    if ($processed)
    {
        header("Location: messages.php") ;
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
    <script type="text/javascript" src="js/messages.js"></script>
    <title>Ajouter/changer les messages</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header">Ajouter/changer les messages</h1>
    </div>
</div>
<form action="messages.php" method="post">
<div class="row">
    <div class="column grid_3 framedInRed">début</div>
    <div class="column grid_3 framedInRed">fin</div>
    <div class="column grid_4 framedInBlue">titre des messages</div>
</div>
<?php
if(count($listeMessages))
{
    foreach($listeMessages as $message)
    {
?>
<div class="row">
            <div id="messageDebut<?=$message->getId()?>"
                class="column grid_3 framedInRed">
                <?=$message->getDebut(true)?>
            </div>
            <div id="messageFin<?=$message->getId()?>"
               class="column grid_3 framedInRed">
               <?=$message->getFin(true)?>
               </div>
            <div id="messageTitre<?$message->getId()?>"
               class="column grid_4 framedInBlue">
<a href="modmessage.php?message=<?=$message->getId()?>&titre=<?=$message->getTitre()?>" title="cliquer pour voir et changer le message"><?=$message->getTitre()?></a>
               </div>
</div>
<?php
        }
    }
?>
<div class="row">
    <div class="column grid_3">
&nbsp;
    </div>
    <div class="column grid_3">
&nbsp;
    </div>
    <div class="column grid_4">
    <a href="modmessage.php" class="button">Nouveau Message</a>
    </div>
</div>
</form>
<?
include("pieddepage.php") ;
?>

</body>
</html>