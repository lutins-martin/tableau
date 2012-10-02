function receptionDesDonnees(data)
{
    if(data.locaux)
    {
        var locaux = data.locaux ;
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
            	for(option in selectDeLocal.options)
        		{
            		var curOption = selectDeLocal.options[option] ;
            		if (curOption.value == locaux[educatriceId].local.id)
            		{
            			curOption.defaultSelected = curOption.selected = true ;           			 
            		}            			
            		else
            		{
            			curOption.selected = curOption.defaultSelected = false ;
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
    success: receptionDesDonnees
    });
    setTimeout(fetchNouvellesDonnees,5000) ;
} ;

$(document).ready(function ()
		{
             document.refreshDelay = setTimeout(fetchNouvellesDonnees,15000) ;
		}
) ;