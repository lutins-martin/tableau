<?php

class PageGroupes extends Page {

    protected $lesGroupes;

    public static function getInstance() {
        if (! isset ( self::$instance )) self::$instance = new self ();
        return self::$instance;
    }

    public function init() {
        parent::init ();
        $this->lesGroupes = Groupes::getInstance ();
        
        $this->addJs ( "ajoute.js" );
        
        $this->titre = "Ajouter/changer les groupes";
        
        $this->recevoirLesDonnees ();
    }

    private function recevoirLesDonnees() {
        $groupesAChanger = $this->getRequestParameter ( 'item' );
        
        if (json_decode ( $groupesAChanger )) {
            $groupesAChanger = json_decode ( $groupesAChanger, true );
        }
        $processed = false;
        if (is_array ( $groupesAChanger )) {
            
            foreach ( $groupesAChanger as $groupeId => $newgroupe ) {
                
                if (isset ( $newgroupe ['efface'] )) {
                    $groupe = $this->lesGroupes->getUnGroupe ( $groupeId );
                    if ($groupe->isLoaded ()) $groupe->delete ();
                    
                    $processed = true;
                    unset ( $groupesAChanger [$groupeId] );
                    continue;
                }
                if (isset ( $newgroupe ['nom'] )) {
                    if (! is_null ( $newgroupe ['nom'] ) && trim ( $newgroupe ['nom'] ) != "") {
                        $groupe = $this->lesGroupes->getUnGroupe ( $groupeId );
                        if ($newgroupe ['nom'] != $groupe->getNom ()) {
                            $groupe->setNom ( $newgroupe ['nom'] );
                            $groupe->save ();
                            $processed = true;
                        }
                    }
                }
            }
        }
        
        $groupesAAjouter = $this->getRequestParameter('itemNouveau');
        if(json_decode($groupesAAjouter)) {
            $decoded = json_decode($groupesAAjouter,true) ;
            $groupesAAjouter = array() ;
            $groupesAAjouter[] = $decoded ;
        }
        if (is_array ( $groupesAAjouter )) {
            foreach ( $groupesAAjouter as $nouveauGroupe ) {
                if (isset ( $nouveauGroupe ['nom'] )) {
                    if (! is_null ( $nouveauGroupe ['nom'] ) && trim ( $nouveauGroupe ['nom'] ) != "") {
                        $groupe = new Groupe ();
                        $groupe->setNom ( $nouveauGroupe ['nom'] );
                        $groupe->save ();
                        $processed = true;
                    }
                }
            }
        }
        
        if ($processed) {
            if (! $this->getRequestParameter ( 'ajax' )) header ( "Location: groupes.php" );
            exit ();
        }
    }

    public function afficheLeContenu() {
        $listeGroupes = $this->lesGroupes->getLesGroupes ();
        ?>
<div id="wrapper" class="row">
	<div id="hd">
		<h1 id="header"><?php echo $this->afficheLeTitre()?></h1>
	</div>
</div>
<?php $this->afficheLeMenu()?>
<form action="groupes.php" method="post">
	<div class="row">
		<div class="column grid_3 valeurItem">cliquez pour effacer</div>
		<div class="column grid_4 nomItem">nom des groupes</div>
	</div>
<?php
        if (count ( $listeGroupes )) {
            foreach ( $listeGroupes as $groupe ) {
                ?>
<div class="row">
		<div id="groupeEfface<?php echo $groupe->getId()?>"
			class="column grid_3 valeurItem">
<?php
                $effaceBox = new CheckBoxNode ( array (
                        "name" => "item[{$groupe->getId()}][efface]" ) );
                print $effaceBox->display ();
                ?>
            </div>
		<div id="groupeNom<?php echo $groupe->getId()?>"
			class="column grid_4 nomItem">
<?php
                $boiteDuNom = new TextBoxNode ( 
                        array (
                                "name" => "item[{$groupe->getId()}][nom]",
                                "value" => $groupe->getNom () ) );
                print $boiteDuNom->display ();
                ?>
               </div>
	</div>
<?php
            }
        }
        ?>
<div class="row">
		<div class="column grid_3">Nouveau groupe:</div>
		<div class="column grid_4">
<?php
        $nouvelleBoiteDuNom = new TextBoxNode ( array (
                "name" => "itemNouveau[][nom]" ) );
        print $nouvelleBoiteDuNom->display ();
        $plus = new ButtonNode ( 
                array (
                        "name" => "ajoute",
                        "value" => "+",
                        "title" => "cliquer ici pour ajouter un autre groupe" ) );
        print $plus->display ();
        ?>
    </div>
	</div>
	<div class="row">
		<div class="column grid_9">
			<input type="submit" value="sauver les changements" disabled /> <input
				type="reset" value="remettre le formulaire à zéro" />
		</div>
	</div>
</form>
<?php
    }
}
?>
