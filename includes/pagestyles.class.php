<?php
class PageStyles extends Page
{
    protected $lesStyles ;

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    public function init()
    {

        parent::init() ;
        $this->lesStyles = Styles::getInstance() ;

        $this->addJs("ajoute.js") ;

        $this->titre = "Ajouter/changer les Styles" ;

        $this->recevoirLesDonnees() ;

    }

    private function recevoirLesDonnees()
    {
        $firePHP = FirePHP::getInstance() ;
        $firePHP->log($_REQUEST,'param') ;
        $StylesAChanger= $this->getRequestParameter('item') ;

        $processed = false ;
        if (is_array($StylesAChanger))
        {
            $firePHP->log($StylesAChanger,"items") ;

            foreach($StylesAChanger as $styleId => $newstyle)
            {
                if(isset($newstyle['actif']) && $newstyle['actif'])
                {
                    $this->LesStyles->setActif($styleId) ;
                }
            }

            foreach($StylesAChanger as $styleId => $newstyle)
            {
                if(isset($newstyle['efface']))
                {
                    $style = $this->lesStyles->getUnstyle($styleId) ;
                    if($style->isLoaded()) $style->delete() ;

                    $processed = true ;
                    unset($StylesAChanger[$styleId]) ;
                    continue ;
                }
                if(isset($newstyle['nom']))
                {
                    if(!is_null($newstyle['nom']) && trim($newstyle['nom'])!="")
                    {
                        $style = $this->lesStyles->getUnstyle($styleId) ;
                        $firePHP->log($style,'object style') ;
                        $aSauver=false ;

                        if($newstyle['nom']!=$style->getNom())
                        {
                            $style->setNom($newstyle['nom']) ;
                            $aSauver=true ;
                            $processed = true ;
                            $firePHP->log($style,'object style') ;
                        }
                        if($newstyle['fichier'] && trim($newstyle['fichier'])!="")
                        {
                            $style->setFichier($newstyle['fichier']) ;
                            $aSauver=true ;
                            $processed = true ;
                        }
                        if($aSauver) $style->save() ;
                    }
                }
            }

        }

        $StylesAAjouter=(isset($_REQUEST['itemNouveau'])?$_REQUEST['itemNouveau']:null) ;
        $firePHP->log($StylesAAjouter,'Styles a ajouter') ;
        if(is_array($StylesAAjouter))
        {
            foreach($StylesAAjouter as $nouveaustyle)
            {
                if(isset($nouveaustyle['nom']))
                {
                    if(!is_null($nouveaustyle['nom']) && trim($nouveaustyle['nom'])!="")
                    {
                        $style = new style();
                        $style->setNom($nouveaustyle['nom']) ;
                        $style->save() ;
                        $processed = true ;
                    }
                }
            }
        }

        if ($processed)
        {
            header("Location: styles.php") ;
            exit ;
        }
    }

    public function afficheLeContenu()
    {
        $firePHP = FirePHP::getInstance() ;
        $listeStyles = $this->lesStyles->getLesStyles() ;
        ?>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header"><?=$this->afficheLeTitre()?></h1>
    </div>
</div>
<? $this->afficheLeMenu()?>
<form action="styles.php" method="post">
<div class="row">
    <div class="column grid_3 valeurItem">cliquez pour effacer</div>
    <div class="column grid_4 nomItem">nom du style</div>
    <div class="column grid_4 nomItem">nom du fichier de style</div>
</div>
<?php
if(count($listeStyles))
{
    foreach($listeStyles as $style)
    {
?>
<div class="row">
            <div id="styleEfface<?=$style->getId()?>"
                class="column grid_3 valeurItem">
<?
        $effaceBox = new CheckBoxNode(array("name" => "item[{$style->getId()}][efface]")) ;
        print $effaceBox->display() ;
?>
            </div>
            <div id="styleNom<?=$style->getId()?>"
               class="column grid_4 nomItem">
<?php
        $boiteDuNom = new TextBoxNode(array("name" => "item[{$style->getId()}][nom]",
        "value" => $style->getNom())) ;
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
    Nouveau style:
    </div>
    <div class="column grid_4">
<?php
    $nouvelleBoiteDuNom = new TextBoxNode(array("name" => "itemNouveau[][nom]")) ;
    print $nouvelleBoiteDuNom->display() ;
    $plus = new ButtonNode(array("name" => "ajoute","value" => "+","title" => "cliquer ici pour ajouter un autre style")) ;
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