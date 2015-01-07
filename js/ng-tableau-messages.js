(function(){
    var app = angular.module('tableau-messages',[]) ;
    

    app.directive('lesMessages', function() {
        return {
            restrict : 'E',
            templateUrl : 'lesMessages.html',
            controller : [ '$sce', function($sce) {
                this.readInMessages = function() {
                    var reader = new commonmark.DocParser();
                    var writer = new commonmark.HtmlRenderer();
                    for (key in tousLesMessages) {
                        var parsed = reader.parse(tousLesMessages[key].corps);
                        tousLesMessages[key].corpsMarkedUp = writer.render(parsed);
                        ;
                    }
                    return tousLesMessages;
                }

                this.trustAsHTML = function(contenu) {
                    return $sce.trustAsHtml(contenu);
                }

                this.tousLesMessages = this.readInMessages();

            } ],
            controllerAs : "messages"
        };
    });

})() ;