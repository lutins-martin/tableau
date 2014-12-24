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
		<h1 id="header"><?php echo $this->afficheLeTitre()?></h1>
	</div>
</div>
<?php $this->afficheLeMenu()?>

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
			<div id="educatrice<?php echo $educatrice->getId()?>"
				class="column grid_4 valeurItem">
				<?php echo "{$educatrice->getNom()} ({$educatrice->getGroupe()->getNom()})"?>
			</div>
			<div id="changementDeLocal<?php echo $educatrice->getId()?>"
				class="column grid_4 nomItem">
				<?php
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