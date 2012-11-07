<?php
class PageDeplacements extends Page
{
    protected $lesGroupes ;
    protected $lesLocaux ;
    protected $lesEducatrices;

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    public function init()
    {

        parent::init() ;
        $this->lesGroupes=Groupes::getInstance() ;
        $this->lesLocaux=Locaux::getInstance() ;
        $this->lesEducatrices=Educatrices::getInstance() ;
        $this->titre = "Dites moi,où sont les moussaillons?" ;

        $this->addJs("tableau.js") ;
        $this->addJs("deplacements.js") ;

    }

    public function afficheLeContenu()
    {
        $firePHP = FirePHP::getInstance() ;
        ?>
<div id="wrapper" class="row">
	<div id="hd">
		<h1 id="header"><?=$this->afficheLeTitre()?></h1>
	</div>
</div>
<? $this->afficheLeMenu()?>

        <?php
        $listeEducatrices = $this->lesEducatrices->getLesEducatrices() ;
        if(count($listeEducatrices))
        {
            foreach($listeEducatrices as $educatrice)
            {
                ?>
<form>
	<div class="row">
		<div class="column boiteAutour grid_9">
			<div id="educatrice<?=$educatrice->getId()?>"
				class="column grid_4 valeurItem">
				<?="{$educatrice->getNom()} ({$educatrice->getGroupe()->getNom()})"?>
			</div>
			<div id="changementDeLocal<?=$educatrice->getId()?>"
				class="column grid_4 nomItem">
				<?
				$selectLocal = new SelectNode(array("name" => "local[{$educatrice->getId()}]",
    "width" => 60));
				$selectLocal->addOption("") ;
				foreach($this->lesLocaux->getLesLocaux() as $local)
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
    }
}
?>