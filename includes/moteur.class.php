<?php
class Moteur extends WebService
{
    protected $lesEducatrices ;
    protected $lesLocaux ;

    const ACTION_TABLEAU_CHANGER_LOCAL = 'changerLocal' ;
    const ACTION_TABLEAU_RELECTURE = 'relecture' ;
    const ACTION_TABLEAU_TOUS_LES_LOCAUX_UNE_EDUCATRICE = 'tousLesLocauxPour' ;

    public static function getInstance()
    {
        if(!isset(self::$instance)) self::$instance= new self() ;
        return self::$instance ;
    }

    public function init()
    {
        parent::init() ;
        $this->lesEducatrices=Educatrices::getInstance() ;
        $this->lesLocaux = Locaux::getInstance() ;
        header("Content-type: application/json") ;
        header("Content: text/html; charset=UTF-8") ;

    }

    public function processRequest()
    {
        $firePHP = FirePHP::getInstance() ;

        $action = $this->getRequestParameter('action') ;

        $firePHP->log($_REQUEST,'request') ;
        switch ($action)
        {
            case self::ACTION_TABLEAU_CHANGER_LOCAL :
                try
                {
                    $listeEducatrice=$this->getRequestParameter('local') ;
                    $firePHP->log($listeEducatrice,'educatrice') ;
                    if(is_array($listeEducatrice))
                    {
                        $firePHP->trace(__FILE__.":".__LINE__) ;
                        foreach($listeEducatrice as $educatriceId => $localId)
                        {
                            $educatrice = $this->lesEducatrices->getUneEducatrice($educatriceId) ;
                            $firePHP->log($educatrice,'educatrice object') ;
                            if($educatrice->isLoaded())
                            {
                                $educatrice->setLocal($localId) ;
                                $educatrice->save() ;
                            }
                        }
                    }
                    print json_encode($output['resultat']=true) ;
                }
                catch (Exception $e)
                {
                    $output['resultat'] = false ;
                    $output['erreur'] = $e->getMessage() ;
                    print json_encode($output) ;
                }
                break;
            case self::ACTION_TABLEAU_RELECTURE :
                header("Content-type: application/json") ;
                $tableau = array() ;
                $listeEducatrices = $this->lesEducatrices->getLesEducatrices() ;
                foreach($listeEducatrices  as $educatrice)
                {
                    $edu['nom'] = $educatrice->getNom() ;
                    $edu['groupe']['nom'] = $educatrice->getGroupe()->getNom() ;
                    $edu['groupe']['id'] = $educatrice->getGroupe()->getId() ;
                    $edu['local']['nom'] = $educatrice->getGroupe()->getLocal()->getNom() ;
                    $edu['local']['id'] = $educatrice->getGroupe()->getLocal()->getId() ;
                    $tableau['locaux'][$educatrice->getId()] = $edu ;
                }
                print json_encode($tableau) ;
                break;
            case self::ACTION_TABLEAU_TOUS_LES_LOCAUX_UNE_EDUCATRICE :
                try
                {
                    $educatriceId=$this->getRequestParameter('educatriceId') ;
                    $educatrice = $this->lesEducatrices->getUneEducatrice($educatriceId) ;
                    $firePHP->log($educatrice,'educatrice object') ;
                    $output['educatriceId'] = $educatriceId ;
                    $output['selected'] = $educatrice->getLocal()->getId() ;
                    if($educatrice->isLoaded())
                    {
                        $lesLocaux = $this->lesLocaux->getLesLocaux() ;
                        foreach($lesLocaux as $local)
                        {
                            $localSimple = array() ;
                            $localSimple['name'] = $local->getNom() ;
                            $localSimple['value'] = $local->getId() ;
                            if($local->getId()==$output['selected'])
                            $localSimple['selected'] = 'selected' ;
                            $output['locaux'][] = $localSimple ;
                        }
                    }
                    print json_encode($output) ;
                }
                catch (Exception $e)
                {
                    $output['resultat'] = false ;
                    $output['erreur'] = $e->getMessage() ;
                    print json_encode($output) ;
                }
                break ;
            default:
                print json_encode($output['resultat']=true) ;
                break ;

        }

    }

}