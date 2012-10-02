<?php
class HtmlNodeException extends Exception
{

}

class HtmlNode
{
    protected $nodeName ;
    protected $attributes = array() ;

    protected $innerHTML ;

    protected $childNodes ;
    protected $parentNode ;

    public function __construct($nodeName,$attributes=null)
    {
        $this->nodeName = strtolower($nodeName) ;
        if(!is_null($attributes)) $this->attributes = $attributes ;
    }

    protected function writeAttributes()
    {
        $output="" ;
        foreach($this->attributes as $name => $value)
        {
            $output .= " {strtolower($name}}=\"$value\"" ;
        }
    }

    public function addAttributes($attributes)
    {
        if (!is_array($attributes)) throw new HtmlNodeException("HtmlNode::addAttributes requires an Array as parm: $attributes") ;
        $this->attributes = merge_array($this->attributes,$attributes) ;
    }

    public function display()
    {
        $output = "<$this->nodeName" ;

        foreach($this->attributes as $attr => $value)
        {
            $output .= " $attr=\"$value\"" ;
        }
        $output .= ">" ;

        if (isset($this->innerHTML)) $output.=$this->innerHTML ;

        if(is_array($this->childNodes))
        {
            foreach($this->childNodes as $childNode)
            {
                $output .= $childNode->display() ;
            }
        }

        $output .= "</$this->nodeName>\n" ;
        return $output ;
    }

    public function setInnerHTML($content)
    {
        $this->innerHTML = $content ;
    }

    public function addChildNode(HtmlNode $node)
    {
        $this->childNodes[] = $node ;
    }
}