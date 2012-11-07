<!--
$(document).ready(function()
{
    $("select").change(function(eventObject)
    {
        var selectField = eventObject.currentTarget ;
        jQuery.ajax("moteur.php?action=changerLocal&"+selectField.name+"="+selectField.value) ;
    }) ;
}) ;
-->