<?php
class PageTableau extends Page
{
    protected $lesEducatrices ;
    protected $lesMessages ;

    public function init()
    {
        parent::init() ;
        $this->lesEducatrices=Educatrices::getInstance() ;
        $this->lesMessages = Messages::getInstance() ;

        $this->addJs("jquery-1.8.2.js") ;
        $this->addJs("tableau.js") ;

        $this->titre = "Où sont les moussaillons?" ;
    }

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    public function afficheLeContenu()
    {
        $firePHP = FirePHP::getInstance() ;
?>
    <div id="dateheure" class="row">
    <div class="column grid_12">
        <h1 id="date" class="date"></h1>
    </div>
    <div class="column grid_4"><h1 id="heure" class="heure"></h1></div>
</div>
<div id="grille" class="column grid_9">
<?php
$listeEducatrices = $this->lesEducatrices->getLesEducatrices() ;
$lesMessageAujourdhui = $this->lesMessages->getLesMessages(true) ;

if(count($listeEducatrices))
{
    foreach($listeEducatrices as $educatrice)
    {
        $firePHP->log($educatrice,'educatrice') ;
?>
<div class="column grid_9">
<div class="row">
        <div class="column boiteAutour grid_8">
            <div id="educatrice<?=$educatrice->getId()?>"
                class="column grid_4 valeurItem">
                <?="{$educatrice->getNom()} ({$educatrice->getGroupe()->getNom()})"?>
            </div>
            <div id="local<?=$educatrice->getId()?>"
                class="column grid_3 nomItem local"><?="{$educatrice->getGroupe()->getLocal()->getNom()}"?></div>
            <div class="column cache boutonEdition"><img id="bouton<?=$educatrice->getId()?>" src="images/group_edit.png" title="cliquez ici pour indiquer un déplacement"></div>
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
<? $this->afficheLeMenu() ;
    }

    public function afficheLeMenu()
    {
        parent::afficheLeMenu("pied") ;
    }
}