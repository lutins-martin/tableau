<?php
class Page extends WebService
{
    protected $listeCss = array();
    protected $listeJs = array() ;
    protected $titre ;

    protected function __construct()
    {
        $this->init() ;
    }

    public function init()
    {
        parent::init() ;
        $gridName = "1248_16_4_4" ;
        $this->addCss("$gridName/reset.css") ;
        $this->addCss("$gridName/text.css") ;
        $this->addCss("$gridName/$gridName.css") ;
        $this->addCss("css/styles/tableau.css") ;

        $lesStyles= Styles::getInstance() ;
        $styleActif = $lesStyles->getFichierStyleActif() ;
        $this->addCss($styleActif,'stylesheet') ;

        $this->addJs("jquery-1.8.2.js") ;
    }

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    private function convertPathRodionov($source)
    {
        $converted = str_replace("/home/martin/www","/var/www/apps.leslutins.ca",$source) ;
        return str_replace("/home/martin/tableau","/var/www/tableau",$converted) ;
    }

    public function addCss($fichierCss,$id=null)
    {
        $fichierCssComplet = $this->convertPathRodionov(stream_resolve_include_path ( $fichierCss )) ;
        $fichierCssWWW=str_replace($_SERVER['DOCUMENT_ROOT'],"http://".$_SERVER['SERVER_NAME'],$fichierCssComplet) ;
        
        if($fichierCssComplet!==false)
        {
            $css['fichier'] = $fichierCssWWW;
            $stat = stat($fichierCssComplet) ;
            $css['modification'] = $stat['mtime'] ;
            $css['id'] = $id ;

            $this->listeCss[] = $css ;
        }
    }

    public function insererTousLesCss()
    {
        if(is_array($this->listeCss))
        {
            $string = " <link __id__ rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"__fichier__\" />" ;
            foreach($this->listeCss as $fichierCss)
            {
                $id = "" ;
                if (isset($fichierCss['id']) && !is_null($fichierCss['id'])) $id = $fichierCss['id'] ;
                $stringAvecFichier = str_replace("__id__","id=\"$id\"",$string) ;
                $stringAvecFichier = str_replace("__fichier__",$fichierCss['fichier']."?r=".$fichierCss['modification'],$stringAvecFichier ) ;
                print $stringAvecFichier."\n" ;
            }
        }
    }

    public function addJs($fichierJs)
    {
        $fichierJsComplet =  $this->convertPathRodionov(stream_resolve_include_path ( $fichierJs )) ;
        $fichierJsWWW=str_replace($_SERVER['DOCUMENT_ROOT'],"http://".$_SERVER['SERVER_NAME'],$fichierJsComplet) ;

        if($fichierJsComplet!==false)
        {
            $Js['fichier'] = $fichierJsWWW;
            $stat = stat($fichierJsComplet) ;
            $Js['modification'] = $stat['mtime'] ;

            $this->listeJs[] = $Js ;
        }
    }

    public function insererTousLesJs()
    {
        if(is_array($this->listeJs))
        {
            $string = "<script type=\"text/javascript\" src=\"__fichier__\"></script>" ;
            foreach($this->listeJs as $fichierJs)
            {
                $stringAvecFichier = str_replace("__fichier__",$fichierJs['fichier']."?r=".$fichierJs['modification'],$string) ;
                print $stringAvecFichier."\n" ;
            }
        }
    }

    public function afficheLeTitre()
    {
        print $this->titre ;
    }

    public function afficheLeMenu($extraClass="")
    {
?>
<div id="menu" class="row $extraClass">
    <div id="petitpied" class="column grid_16">
        <ul class="menu">
            <li><a href="index.php" class="menu">Tableau</a>
            </li>
            <li><a href="deplacements.php" class="menu">déplacements</a>
            </li>
            <li><a href="locaux.php" class="menu">locaux</a>
            </li>
            <li><a href="groupes.php" class="menu">groupes</a>
            </li>
            <li><a href="educatrices.php" class="menu">éducatrices(eurs)</a>
            </li>
            <li><a href="messages.php" class="menu">messages</a>
            </li>
            <li><a href="styles.php" class="menu">thèmes</a>
            </li>
        </ul>
    </div>
</div>

<?
    }
}