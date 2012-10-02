<?php
class ResetButtonNode extends InputNode
{
    public function __construct($attributes=null)
    {
        $this->nodeName = 'INPUT';
        $attributes['TYPE'] = 'reset' ;
        if(!is_null($attributes)) $this->attributes = $attributes ;
    }
}
?>