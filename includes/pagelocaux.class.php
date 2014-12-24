<?php
class PageLocaux extends Page
{
    protected $lesLocaux ;

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    public function init()
    {
        parent::init() ;
        $this->lesLocaux = Locaux::getInstance() ;

        $this->addJs("ajoute.js") ;

        $this->recevoirLesDonnees() ;
    }

    private function recevoirLesDonnees()
    {
        $locauxAChanger= $this->getRequestParameter('item') ;

        $processed = false ;

        if (is_array($locauxAChanger))
        {
            foreach($locauxAChanger as $localId => $newLocal)
            {
                if(isset($newLocal['efface']))
                {
                    $local = $this->lesLocaux->getUnLocal($localId) ;
                    if($local->isLoaded()) $local->delete() ;

                    $processed = true ;
                    unset($locauxAChanger[$localId]) ;
                    continue ;
                }
                if(isset($newLocal['nom']))
                {
                    if(!is_null($newLocal['nom']) && trim($newLocal['nom'])!="")
                    {
                        $local = $this->lesLocaux->getUnLocal($localId) ;
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

        $locauxAAjouter=$this->getRequestParameter('itemNouveau') ;
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
    }

    public function afficheLeContenu()
    {
        $firePHP = FirePHP::getInstance() ;
        ?>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header">Ajouter/changer les locaux</h1>
    </div>
</div>
<?php $this->afficheLeMenu()?>
<form action="locaux.php" method="post">
<div class="row">
    <div class="column grid_3 valeurItem">cliquez pour effacer</div>
    <div class="column grid_4 nomItem">nom des locaux</div>
</div>
<?php
$listeLocaux = $this->lesLocaux->getLesLocaux() ;

if(count($listeLocaux))
{
    foreach($listeLocaux as $local)
    {
?>
<div class="row">
            <div id="localEfface<?php echo $local->getId()?>"
                class="column grid_3 valeurItem">
<?php
        $effaceBox = new CheckBoxNode(array("name" => "item[{$local->getId()}][efface]")) ;
        print $effaceBox->display() ;
?>
            </div>
            <div id="localNom<?php echo $local->getId()?>"
               class="column grid_4 nomItem">
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
				<?php
            }
}
?>