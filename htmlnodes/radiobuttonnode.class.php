<?php
class RadioButtonNode extends InputNode
{
    public function __construct($attributes=null)
    {
        $this->nodeName = 'INPUT';
        $attributes['TYPE'] = 'radio' ;
        if(!is_null($attributes)) $this->attributes = $attributes ;
    }
}
?>