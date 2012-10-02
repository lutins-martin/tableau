<?php
class ButtonNode extends InputNode
{
    public function __construct($attributes=null)
    {
        $this->nodeName = 'INPUT';
        $attributes['TYPE'] = 'button' ;
        if(!is_null($attributes)) $this->attributes = $attributes ;
    }
}
?>