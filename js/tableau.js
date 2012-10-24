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
    if(data.locaux)
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
} ;

function fetchNouvellesDonnees()
{
    $.ajax({
	    url : "moteur.php?deplacement=relecture",
	    cache : false,
	    success: receptionDesDonnees
    });
    setTimeout(fetchNouvellesDonnees,15000) ;
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
             document.refreshDelay = setTimeout(fetchNouvellesDonnees,5000) ;
             relireLheure() ;
		}
) ;