<?php
class PageEducatrices extends Page
{
    protected $lesGroupes ;
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
        $this->lesEducatrices=Educatrices::getInstance() ;
        $this->titre = "Ajouter/changer les éducatrices" ;

        $this->addJs("ajoute.js") ;

        $this->recevoirLesDonnees() ;
    }

    public function recevoirLesDonnees()
    {
        $educatricesAChanger= $this->getRequestParameter('item') ;

        $processed = false ;

        if (is_array($educatricesAChanger))
        {

            foreach($educatricesAChanger as $educatriceId => $neweducatrice)
            {
                if(isset($neweducatrice['efface']))
                {
                    $educatrice = $this->lesEducatrices->getUneEducatrice($educatriceId) ;
                    if($educatrice->isLoaded()) $educatrice->delete() ;

                    $processed = true ;
                    unset($educatricesAChanger[$educatriceId]) ;
                    continue ;
                }

                $educatrice = $this->lesEducatrices->getUneEducatrice($educatriceId) ;
                if(isset($neweducatrice['nom']))
                {
                    $firePHP->log($neweducatrice,'neweducatrice') ;
                    if(!is_null($neweducatrice['nom']) && trim($neweducatrice['nom'])!="")
                    {
                        if($neweducatrice['nom']!=$educatrice->getNom())
                        {
                            $educatrice->setNom($neweducatrice['nom']) ;
                            $educatrice->save() ;
                            $processed = true ;
                            $firePHP->log($educatrice,'object educatrice') ;
                        }
                    }
                }
                if(isset($neweducatrice['groupe']))
                {
                    if($neweducatrice['groupe']!=$educatrice->getGroupe()->getId())
                    {
                        $educatrice->setGroupe($this->lesGroupes->getUnGroupe($neweducatrice['groupe'])) ;
                        $educatrice->save() ;
                        $processed = true ;
                    }
                }
            }

        }

        $educatricesAAjouter = (isset($_REQUEST['itemNouveau'])?$_REQUEST['itemNouveau']:null) ;
        if(is_array($educatricesAAjouter))
        {
            foreach($educatricesAAjouter as $nouvelleEducatrice)
            {
                if(isset($nouvelleEducatrice['nom']))
                {
                    if(!is_null($nouvelleEducatrice['nom']) && trim($nouvelleEducatrice['nom'])!="")
                    {
                        $local = new Educatrice();
                        $local->setNom($nouvelleEducatrice['nom']) ;
                        $local->save() ;
                        $processed = true ;
                    }
                }
            }
        }
        if ($processed)
        {
            header("Location: educatrices.php") ;
            exit ;
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
<? $this->afficheLeMenu()?>
<form action="educatrices.php" method="post">
<div class="row">
    <div class="column grid_3 valeurItem">cliquez pour effacer</div>
    <div class="column grid_4 nomItem">nom des éducatrices</div>
    <div class="column grid_4 nomItem">groupe</div>
</div>
<?php
$listeEducatrices = $this->lesEducatrices->getLesEducatrices() ;
$tousLesGroupes = $this->lesGroupes->getLesGroupes() ;
if(count($listeEducatrices))
{
    foreach($listeEducatrices as $educatrice)
    {
?>
<div class="row">
            <div id="educatriceEfface<?=$educatrice->getId()?>"
                class="column grid_3 valeurItem">
<?
        $effaceBox = new CheckBoxNode(array("name" => "item[{$educatrice->getId()}][efface]")) ;
        print $effaceBox->display() ;
?>
            </div>
            <div id="educatriceNom<?=$educatrice->getId()?>"
               class="column grid_4 nomItem">
<?php
        $boiteDuNom = new TextBoxNode(array("name" => "item[{$educatrice->getId()}][nom]",
        "value" => $educatrice->getNom())) ;
        print $boiteDuNom->display() ;
?>
               </div>
               <div id="educatriceGroupe<?=$educatrice->getId()?>" class="column grid_4 nomItem">
<?php
        $selectDeGroupe = new SelectNode(array("name" => "item[{$educatrice->getId()}][groupe]")) ;
        $selectDeGroupe->addOption("",0) ;
        foreach($tousLesGroupes as $groupe)
        {
            $firePHP->log($groupe,"groupe dans create select") ;
            $selectDeGroupe->addOption($groupe->getNom(),$groupe->getId(),($groupe->getId()==$educatrice->getGroupe()->getId())) ;
        }
        print $selectDeGroupe->display() ;
?>
               </div>
</div>
<?php
        }
    }
?>
<div class="row">
    <div class="column grid_3">
    Nouvelle éducatrice(teur):
    </div>
    <div class="column grid_4">
<?php
    $nouvelleBoiteDuNom = new TextBoxNode(array("name" => "itemNouveau[][nom]")) ;
    print $nouvelleBoiteDuNom->display() ;
    $plus = new ButtonNode(array("name" => "ajoute","value" => "+","title" => "cliquer ici pour ajouter une autre éducatrice(teur)")) ;
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