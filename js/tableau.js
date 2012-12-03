Date.prototype.getDateEnFrancais = function()
{
	var jourSemaine = Array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi") ;
	var moisDeLannee = Array("janvier","février","mars","avril","mai","juin","juillet",
			"août","septembre","octobre","novembre","décembre") ;
	
	var jour = this.getDay() ;
	var mois = this.getMonth() ;
	
	return jourSemaine[jour] + ", le " + this.getDate().toString() + " " + moisDeLannee[mois] + " " + this.getFullYear() ; 
}; 

Date.prototype.getHeure = function(avecSecondes)
{
	var minutes = this.getMinutes() ;
	var minutesString = minutes.toString() ;
	if(minutes<10) minutesString = "0" + minutesString ;
	
	var lHeure = this.getHours()+":"+minutesString  ;
	if(avecSecondes)
	{
		var secondes = this.getSeconds() ;
		var secondesString = secondes.toString() ;
		if(secondes<10) secondesString = "0"+secondesString ;
		lHeure += ":"+secondesString ;
	}	
	return lHeure ;
}

function receptionDesDonnees(data)
{
    if(data.locaux && !document.refuseChangement)
    {
        var locaux = data.locaux ;
        var rangees= $("div[id^='educatrice']").length ;
        var count=0 ;
        for(local in locaux) { count++ } ;
        if(count!=rangees) { location.reload() ; } ; /* change in count is too complex to handle, just reload the page */
        
        for(educatriceId in locaux)
        {
            var divPourEducatrice = $("#educatrice"+educatriceId)[0] ;
            divPourEducatrice.innerHTML = locaux[educatriceId].nom + " (" + locaux[educatriceId].groupe.nom + ")" ;
            var divPourLocal = $("#local"+educatriceId)[0] ;
            if(divPourLocal)
            {
            	divPourLocal.innerHTML = locaux[educatriceId].local.nom ;
            }
            
            
            var divChangementDeLocal = $("#changementDeLocal"+educatriceId)[0];
            if(divChangementDeLocal)
            {
            	var selectDeLocal = $(divChangementDeLocal).find("select")[0] ; 
            	for(index=0;index<selectDeLocal.options.length;index++)
        		{
            		var curOption = selectDeLocal.options[index] ;
            		if (curOption.value == locaux[educatriceId].local.id)
            		{
            			selectDeLocal.options.selectedIndex = index ;           			 
            		}            			            			
        		}
            	//= locaux[educatriceId].local.id ;
            }
            
        } ;
    } ;
    
    if(data.dernierChangement && !document.refuseChangement)
    {
    	var dernierChangementIci = $("#dernierChangementIci") ;
    	if(dernierChangementIci.length==1)
    	{
    		dernierChangementIci = dernierChangementIci[0] ;
    		if (data.dernierChangement > dernierChangementIci.value)
    		{
    			location.reload() ;
    		}
    	}
    }

} ;

function afficherDeplacement()
{
	document.refuseChangement = true ;
	
	var selectExisteDeja = $(this.parentNode.parentNode).find("select") ;
	if (selectExisteDeja.length)
	{
		var selectObject = selectExisteDeja[0] ;
		var parent = selectObject.parentNode ;
		
		var preferredSibling = selectObject ;
		while(preferredSibling.nodeName!="#text") preferredSibling = preferredSibling.previousSibling ;

		preferredSibling.data = selectObject.siblingText;
		parent.removeChild(selectObject) ;
		document.refuseChangement = false ;
		this.src = this.oldsrc ;
		this.title = this.oldtitle ;
	}
	else
	{
		this.oldsrc = this.src ;
		this.src = "images/cancel.png" ;
		
		this.oldTitle = this.title ;
		this.title="cliquer pour annuler le déplacement" ;
		
		var educatriceId = this.id.replace("bouton","") ;
		
		var boitesLocal = $(this.parentNode.parentNode).find(".local") ;
		
		$.ajax({
			url: "moteur.php?action=tousLesLocauxPour&educatriceId=" + educatriceId ,
			context : boitesLocal[0],
			cache: false
		}).done(function (data)
		{
			var selectObject = document.createElement("select") ;
			selectObject.name = "local[" + data.educatriceId + "]" ;
			selectObject.width = 60 ;
			$(selectObject).hide() ;
			data.locaux.forEach(function(local){
				var optionObject = document.createElement("option") ;
				optionObject.value = local.value ;
				optionObject.innerHTML = local.name ;
				selectObject.appendChild(optionObject) ;
			}) ;
			this.appendChild(selectObject) ;
			var preferredSibling = selectObject ;
			while(preferredSibling.nodeName!="#text") preferredSibling = preferredSibling.previousSibling ;

			selectObject.siblingText = preferredSibling.data ;
			preferredSibling.data = "" ;
			$(selectObject).show() ;
			$(selectObject).change(function(eventObject)
					{
				document.refuseChangement = false ;
				var selectField = eventObject.currentTarget ;
				jQuery.ajax("moteur.php?action=changerLocal&"+selectField.name+"="+selectField.value).done(
                                    function()
                                    {
                                       location.reload() ;
                                    }) ;
					}) ;

		}) ;		
	}
}

function afficheEdit()
{
	var boutonEditionListe = $(this).find(".boutonEdition")
	$(boutonEditionListe).show() ;
}

function cacheEdit()
{
	var boutonEditionListe = $(this).find(".boutonEdition")
	$(boutonEditionListe).hide() ;	
}

function fetchNouvellesDonnees()
{
	if(!document.refuseChangement)
	{
		$.ajax({
		    url : "moteur.php?action=relecture",
		    cache : false,
		    success: receptionDesDonnees
	    });
		
		setTimeout(fetchNouvellesDonnees,15000) ;
	}    
    
} ;

function relireLheure()
{
	var maintenant = new Date() ;
	$("#heure").each(function()
			{
				$(this).html("&nbsp;"+ maintenant.getHeure() + "&nbsp;") ;
			}
			) ;
	$("#date").each(function()
			{
				$(this).html(maintenant.getDateEnFrancais()) ;
			}
			) ;
	document.horlogeDelay = setTimeout(relireLheure,1000) ;
}

$(document).ready(function ()
		{
			 $(".boiteAutour").mouseover(afficheEdit) ;			 
			 $(".boiteAutour").mouseout(cacheEdit) ;
			 
			 $(".boiteAutour img").click(afficherDeplacement) ;
             document.refreshDelay = setTimeout(fetchNouvellesDonnees,5000) ;
             relireLheure() ;
		}
) ;
