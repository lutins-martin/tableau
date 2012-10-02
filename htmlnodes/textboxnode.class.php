<?php
class TextBoxNode extends InputNode
{
    public function __construct($attributes=null)
    {
        $this->nodeName = 'INPUT';
        $attributes['TYPE'] = 'text' ;
        if(!is_null($attributes)) $this->attributes = $attributes ;
    }
}
?>