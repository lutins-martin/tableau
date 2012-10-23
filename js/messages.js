$(document).ready(function() 
		{
			$("#messageBox").htmlarea({
                toolbar: [/*"html", "|",*/
                          "forecolor",  // <-- Add the "forecolor" Toolbar Button
                          "|", "bold", "italic", "underline", "|", "p", "h1", "h2", "h3", "|","orderedList","unorderedList","link", "unlink"], // Overrides/Specifies the Toolbar buttons to show
				toolbarText: $.extend({}, jHtmlArea.defaultOptions.toolbarText, {
		        "bold": "gras",
		        "italic": "italique",
		        "underline": "souligné",
		        "p": "paragraphe normal",
		        "h1": "titre niveau 1",
		        "h2": "titre niveau 2",
		        "h3": "titre niveau 3",
		        "link": "ajouter un hyperlien",
		        "unlink": "retirer l'hyperlien",
		        "orderedlist" : "liste numérotée",
		        "unorderedlist" : "liste non-numérotée"
		    }) 
                  }) ;
		}) ;