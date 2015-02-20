(function() {
    var app = angular.module('tableau-backgrounds', []);

    app.controller('BackgroundController', [
            '$http',
            function($http) {
                this.backgrounds = [];
                this.tousLesBackgrounds = [];
                this.selectedBackgrounds = [];
                this.allSelected = false;
                this.nouveaubackground = "";
                this.active = {};

                var theBackgroundsController = this;

                this.relecture = function() {
                    $http.get("moteur.php", {
                        params : {
                            action : "tousLesBackgrounds"
                        }
                    }).success(function(data) {
                        theBackgroundsController.backgrounds = data.backgrounds;
                        theBackgroundsController.tousLesBackgrounds = [];
                        theBackgroundsController.selectedBackgrounds = [];
                        theBackgroundsController.allSelected = false;
                        theBackgroundsController.nouveaubackground.name = "";
                        for (key in theBackgroundsController.backgrounds) {
                            if (theBackgroundsController.backgrounds[key].active) {
                                theBackgroundsController.active = theBackgroundsController.backgrounds[key];
                            }
                        }
                    }).error(function(data) {
                    });
                };

                this.changeBackground = function(background) {
                    // document.documentElement.style.backgroundImage =
                    // background.file
                    // ;
                    if (background.selected) {
                        this.select(background)
                    }
                    $('html').css('backgroundImage', 'url(' + background.file + ')');
                    this.active = background;
                    for (key in this.backgrounds) {
                        this.backgrounds[key].active = false;
                    }
                    background.active = true;
                    $http.post("moteur.php", {
                        action : "changeBackground",
                        background : background
                    });
                }

                this.select = function(background) {
                    if (!background.active) {
                        if (typeof background.selected == "undefined") {
                            background.selected = true;
                        } else {
                            background.selected = !background.selected;
                        }
                        if (background.selected) {
                            this.selectedBackgrounds.push(background.value);
                        } else {
                            var index = this.selectedBackgrounds.indexOf(background.value);
                            if (index > -1) {
                                this.selectedBackgrounds.splice(index, 1);
                            }
                        }
                    }
                };

                this.saveBackground = function(background) {
                    background.editing = false;
                };

                this.effaceBackgrounds = function() {
                    if (confirm("Vous allez définitivement effacer ces images, sans possibilité de les récupérer. Jamais.\n"
                            + "Cliquez sur OK pour confirmer votre intention.")) {
                        if (confirm("Confirmez à nouveau que vous voulez vraiment effacer ce ou ces fichiers?")) {
                            var items = [];
                            var backgroundToDelete = {};
                            for (key in this.backgrounds) {
                                if (this.backgrounds[key].selected) {
                                    items.push(this.backgrounds[key]);
                                }
                            }
                            $http.post("moteur.php", {
                                action : "effacerBackgroundFiles",
                                files : items,
                            }).success(function() {
                                theBackgroundsController.relecture();
                            });
                        }
                    }

                };

                this.ajouteBackground = function() {
                    var nouveauBackground = [ {
                        nom : theBackgroundsController.nouveaubackground.name
                    } ];
                    $http.get("locaux.php", {
                        params : {
                            itemNouveau : nouveauBackground,
                            ajax : 1
                        }
                    }).success(function() {
                        theBackgroundsController.relecture();
                    });
                };

                this.relecture();
            } ]);

})();