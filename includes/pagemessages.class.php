<?php
class PageMessages extends Page
{
    protected $lesMessages ;

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    public function init()
    {
        parent::init() ;
        $this->lesMessages = Messages::getInstance() ;

        $this->recevoirLesDonnees() ;
        $this->titre = "Ajouter/changer les messages" ;

        $this->addJs("messages.js") ;
    }

    private function recevoirLesDonnees()
    {
        $messagesAChanger = $this->getRequestParameter('item') ;

        if (is_array($messagesAChanger))
        {
            $processed = false ;

            foreach($messagesAChanger as $messageID => $newMessage)
            {
                if(isset($newMessage['efface']))
                {
                    $message = $this->lesMessage->getUnMessage($messageID) ;
                    if($message->isLoaded()) $message->delete() ;

                    $processed = true ;
                    unset($messagesAChanger[$messageID]) ;
                    continue ;
                }
                if(isset($newMessage['message']))
                {
                    if(!is_null($newMessage['message']) && trim($newMessage['message'])!="")
                    {
                        $message = $this->lesMessages->getUnMessage($messageID) ;
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
    }

    public function afficheLeContenu()
    {
        $firePHP = FirePHP::getInstance() ;
?>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header"><?php $this->afficheLeTitre()?></h1>
    </div>
</div>
<?php $this->afficheLeMenu()?>
<form action="modmessage.php" method="post">
<div class="row">
    <div class="column grid_2 nomItem">effacer</div>
    <div class="column grid_3 valeurItem">début</div>
    <div class="column grid_3 valeurItem">fin</div>
    <div class="column grid_4 nomItem">titre des messages</div>
</div>
<?php
$listeMessages = $this->lesMessages->getLesMessages() ;
if(count($listeMessages))
{
    foreach($listeMessages as $message)
    {
?>
<div class="row">
            <div id="messageEfface<?php echo $message->getId()?>"
                class="column grid_2 nomItem">
                <input type="checkbox" name="messageEfface[<?php echo $message->getId()?>]" value="on"></input>
            </div>
            <div id="messageDebut<?php echo $message->getId()?>"
                class="column grid_3 valeurItem">
                <?php echo $message->getDebut(true)?>
            </div>
            <div id="messageFin<?php echo $message->getId()?>"
               class="column grid_3 valeurItem">
               <?php echo $message->getFin(true)?>
               </div>
            <div id="messageTitre<?$message->getId()?>"
               class="column grid_4 nomItem">
<a href="modmessage.php?messageId=<?php echo $message->getId()?>&titre=<?php echo $message->getTitre()?>" title="cliquer pour voir et changer le message"><?php echo $message->getTitre()?></a>
               </div>
</div>
<?php
        }
    }
?>
<div class="row">
    <div class="column grid_3"><input type="submit" name="submit" value="effacer ces messages"></input></div>
    <div class="column grid_2">
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
<?php
    }
}
?>