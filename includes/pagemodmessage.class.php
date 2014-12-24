<?php
class PageModMessage extends Page
{
    protected $lesMessages ;
    protected $monMessage ;

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    public function init()
    {
        global $tableauRootDir ;
        parent::init() ;
        $this->lesMessages=Messages::getInstance() ;
        $this->titre = "Ajouter/changer un message" ;

        $this->addJs("messages.js") ;

        set_include_path(get_include_path().":$tableauRootDir/tableau/jHtmlArea-0.7.5/scripts/:$tableauRootDir/tableau/jquery-ui-1.9.0.custom/js/:$tableauRootDir/tableau/jHtmlArea-0.7.5/style/:$tableauRootDir/tableau/:jquery-ui-1.9.0.custom/css/smoothness/") ;

        $this->addJs("jHtmlArea-0.7.5.min.js") ;
        $this->addJs("jHtmlArea.ColorPickerMenu-0.7.0.js") ;
        $this->addJs("jquery-ui-1.9.0.custom.js") ;

        $this->addCss("jHtmlArea.css") ;
        $this->addCss("jHtmlArea.ColorPickerMenu.css") ;
        $this->addCss("jquery-ui-1.9.0.custom.css") ;

        $this->recevoirLesDonnees() ;


    }

    public function recevoirLesDonnees()
    {
        $firePHP = FirePHP::getInstance() ;

        $messageId = $this->getRequestParameter('messageId') ;

        $this->monMessage = $this->lesMessages->getUnMessage($messageId) ;

        $firePHP->log($this->monMessage,"message $messageId") ;

        if($this->getRequestParameter('submit'))
        {
            $messagesAEffacer = array() ;
            if($this->getRequestParameter('messageEfface'))
            {
                $messagesAEffacer = array_keys($this->getRequestParameter('messageEfface')) ;
                foreach($messagesAEffacer as $messageId)
                {
                    $this->monMessage = $this->lesMessages->getUnMessage($messageId) ;
                    if($this->monMessage->isLoaded())
                    {
                        $this->monMessage->delete() ;
                    }
                }
                header("Location: messages.php") ;
                exit ;
            }
            else
            {
                $this->monMessage->setTitre($_REQUEST['titre']) ;
                $this->monMessage->setMessage($_REQUEST['message']) ;
                $this->monMessage->setDebut($_REQUEST['debut']) ;
                $this->monMessage->setFin($_REQUEST['fin']) ;

                $this->monMessage->save() ;
            }
            header("Location: messages.php") ;
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
<input type="hidden" name="messageId" value="<?php echo $this->monMessage->getId()?>"></input>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_4 valeurItem">début</div>
    <div class="column grid_4 valeurItem">fin</div>
</div>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_4 valeurItem"><input type="text" name="debut" value="<?php echo $this->monMessage->getDebut(true)?>" size="10" class="dateInput"></input></div>
    <div class="column grid_4 valeurItem"><input type="text" name="fin" value="<?php echo $this->monMessage->getFin(true)?>" size="10" class="dateInput"></div>
</div>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_13 nomItem"><label for="titreMessage">Titre </label>
    <input id="titreMessage" type="text" name="titre" value="<?php echo $this->monMessage->getTitre()?>" size="100"></input></div>
</div>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_13 nomItem"><textarea id="messageBox" name="message" cols="80" rows="20"><?php echo $this->monMessage->getMessage()?></textarea></div>
</div>
<div class="row">
    <div class="column grid_2">&nbsp;</div>
    <div class="column grid_8 valeurItem"><input type="reset" name="reset" value="annuler les modifications"></input>&nbsp;
    <input type="submit" name="submit" value="sauvegarder les changements"></input></div>
</div>
</form>
				<?php
    }
}
?>