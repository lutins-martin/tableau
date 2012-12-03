function attacheStylesReadyProcessor()
{
	$("input[type='radio']").click(function()
			{
		var nomFichierList = $(this.parentNode.parentNode).find("input[type='text']") ;
		if ((nomFichierList) && (nomFichierList.length))
		{
			$(nomFichierList).each(function()
				{
					if(this.name.indexOf('fichier')!=-1)
					{
						var stylesheet = $("#stylesheet")[0] ;
						var re = /\/styles\/[^?]+/ ;
						stylesheet.href = stylesheet.href.replace(re,"/styles/"+this.value) ;				
					}				
				}) ;
		}
			}) ;
}