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
        $this->addCss("styles/style.css") ;

        $this->addJs("jquery-1.8.2.js") ;


    }

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    private function convertPathRodionov($source)
    {
        return str_replace("/home/martin/www","/var/www/apps.leslutins.ca",$source) ;
    }

    public function addCss($fichierCss)
    {
        $fichierCssComplet = $this->convertPathRodionov(stream_resolve_include_path ( $fichierCss )) ;
        $fichierCssWWW=str_replace($_SERVER['DOCUMENT_ROOT'],"http://".$_SERVER['SERVER_NAME'],$fichierCssComplet) ;

        if($fichierCssComplet!==false)
        {
            $css['fichier'] = $fichierCssWWW;
            $stat = stat($fichierCssComplet) ;
            $css['modification'] = $stat['mtime'] ;

            $this->listeCss[] = $css ;
        }
    }

    public function insererTousLesCss()
    {
        if(is_array($this->listeCss))
        {
            $string = " <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"__fichier__\" />" ;
            foreach($this->listeCss as $fichierCss)
            {
                $stringAvecFichier = str_replace("__fichier__",$fichierCss['fichier']."?r=".$fichierCss['modification'],$string) ;
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
        </ul>
    </div>
</div>

<?
    }
}