<?php
class SubmitButtonNode extends InputNode
{
    public function __construct($attributes=null)
    {
        $this->nodeName = 'INPUT';
        $attributes['TYPE'] = 'submit' ;
        if(!is_null($attributes)) $this->attributes = $attributes ;
    }
}
?>