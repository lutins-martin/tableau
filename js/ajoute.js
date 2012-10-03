
function ajouteUneSectionNouveau(eventObject)
{
    var divADupliquer = eventObject.currentTarget.parentNode.parentNode ;
    var divNouveau=divADupliquer.cloneNode(true) ;

    divADupliquer.parentNode.insertBefore(divNouveau,divADupliquer.nextSibling)
    var leBoutonAjoute = $(divNouveau).find("input[name='ajoute'][type='button']")[0] ;
    leBoutonAjoute.parentNode.removeChild(leBoutonAjoute) ;
    var inputTextNouveau = $(divNouveau).find("input[name^='item'][type='text']")[0] ;
    inputTextNouveau.value = "" ;
} ;

function enableOrDisableSubmitButton(eventObject)
{
	var enableSubmit = false ;

	var theForm = eventObject.currentTarget.form ;
	$(theForm).find("input,select").each(function(i,item)
	{
		if(item.type=='checkbox')
		{
			if(item.checked!=item.defaultChecked)
			{
				enableSubmit=true ;
				return ;
			};
		}
		if(item.type=='text')
		{
			if(item.value!=item.defaultValue)
			{
				enableSubmit=true ;
				return ;
			};
		}
		if(item.type=='select-one')
		{
			/* walk trough the options */
			for(opt in item.options)
			{
				if(item.options[opt].selected!=item.options[opt].defaultSelected)
				{
					enableSubmit=true ;
					return ;
				}
			}
		}
	}) ;

	$(theForm).find("input[type='submit']").each(function(i,item){item.disabled=!enableSubmit}) ;
}

function readyProcessor()
{
    var listeBoutonsAjoute = $("form input[name='ajoute']") ;
    listeBoutonsAjoute.each(function(i,bouton)
    {
        if(bouton.type='button') $(bouton).click(ajouteUneSectionNouveau) ;
    }) ;
    
    $("form input,select").keyup(enableOrDisableSubmitButton) ;
    $("form input,select").click(enableOrDisableSubmitButton) ;
    $("form select").change(enableOrDisableSubmitButton) ;

    var listeBoutonSubmit = $("form input[type='submit']") ;
    $(listeBoutonSubmit).click(function()
	{
    	var deleting = Array() ;
    	var listCheckBox = $("form input[type='checkbox']") ;
    	$(listCheckBox).each(function (i,checkBox)
		{
    		if(checkBox.checked)
			{
    			var rowParent = checkBox.parentNode.parentNode ;
    			var label = $(rowParent).find("input[type='text']")[0].value ;
    			deleting.push(label) ;
			}    			
		}) ;
    	
    	if(deleting.length>0)
		{
    		var confirmation = "Vous effacez:\n\n" ;
    		for(entree in deleting)
			{
    			confirmation += "-> " + deleting[entree] + "\n" ;
			}
    		confirmation += "\nDésirez-vous continuer?" ;
    		return confirm(confirmation) ;
		}
    }) ;

} ;

$(document).ready(readyProcessor) ;
