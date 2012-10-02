<?php
class InputNode extends HtmlNode
{
    public function __construct($attributes=null)
    {
        $this->nodeName = 'INPUT';
        $attributes['TYPE'] = 'checkbox' ;
        if(!is_null($attributes)) $this->attributes = $attributes ;
    }

    public function setInnerHTML($content)
    {
        return ;
    }

    public function display()
    {
        $output = "<$this->nodeName" ;

        foreach($this->attributes as $attr => $value)
        {
            $output .= " $attr=\"$value\"" ;
        }
        $output .= "/>\n" ;

        return $output ;
    }
}
?>