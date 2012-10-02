<?php
class CheckBoxNode extends InputNode
{
    public function __construct($attributes=null)
    {
        $this->nodeName = 'INPUT';
        $attributes['TYPE'] = 'checkbox' ;
        if(!is_null($attributes)) $this->attributes = $attributes ;
    }
}
?>