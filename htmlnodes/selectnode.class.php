<?php
class OptionNode extends HtmlNode
{
    public function __construct($attributes=null)
    {
        parent::__construct("OPTION",$attributes) ;
    }
}

class OptionGroupNode extends HtmlNode
{

}

class SelectNode extends HtmlNode
{
    public function __construct($attributes)
    {
        parent::__construct("SELECT",$attributes) ;
    }

    public function addOption($displayName,$value=null,$selected=false)
    {
        $attributes = array() ;
        if (!is_null($value)) $attributes['value']=$value ;

        if ($selected) $attributes['selected'] = "selected" ;

        $optionNode = new OptionNode((count($attributes)?$attributes:null)) ;
        $optionNode->setInnerHTML($displayName) ;

        $this->addChildNode($optionNode) ;
    }
}