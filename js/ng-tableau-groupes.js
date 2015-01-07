(function() {
    var app = angular.module('tableau-groupes',[]) ;
    
    
    app.controller('GroupesController', [ '$http', function($http) {
        this.groupes;
        this.groupesSelected = [];
        this.allSelected = false;
        this.nouveaugroupe = "";

        var theGroupesController= this;

        this.relecture = function() {
            $http.get("moteur.php", {
                params : {
                    action : "tousLesGroupes"
                }
            }).success(function(data) {
                theGroupesController.groupes = data.groupes;
                theGroupesController.tousLesGroupes = [];
                theGroupesController.groupesSelected = [];
                theGroupesController.allSelected = false;
                theGroupesController.nouveaugroupe.nom = "";
            }).error(function(data) {
            });
        };

        this.editGroupe = function(groupe) {
            /* clear any other edit field, save them if dirty */
            for (key in this.groupes) {
                if (this.groupes[key].editing) {
                    this.saveGroupe(this.groupes[key]);
                }
            }
            groupe.editing = true;
        };

        this.stopEditGroupe = function(groupe) {
            groupe.editing = false;
        };

        this.selectAll = function(all) {
            this.groupesSelected = [] ;
            for (key in this.groupes) {
                this.groupes[key].selected = all;
                if(all) {
                    this.groupesSelected.push(this.groupes[key].value) ;
                }
            }
            this.allSelected = all;
        };

        this.select = function(groupe) {            
            if (typeof groupe.selected == "undefined") {
                groupe.selected = true;
            } else {
                groupe.selected = !groupe.selected;
            }
            if(groupe.selected) {
                this.groupesSelected.push(groupe.value);
            } else {
                var index = this.groupesSelected.indexOf(groupe.value);
                if (index > -1) {
                    this.groupesSelected.splice(index, 1);
                }
            }
        };

        this.saveGroupe = function(groupe) {
            groupe.editing = false;
            var item = {};
            item[groupe.valeur] = {
                nom : groupe.nom
            };
            $http.get("groupes.php", {
                params : {
                    item : item,
                    ajax : 1
                }
            });
        };

        this.effaceGroupes = function() {
            var item = {};
            var groupeToDelete = {};
            for (key in this.groupes) {
                if (this.groupes[key].selected) {
                    var groupeToDelete = {
                        efface : 1
                    };
                    item[this.groupes[key].valeur] = groupeToDelete;
                }
            }
            $http.get("groupes.php", {
                params : {
                    item : item,
                    ajax : 1
                }
            }).success(function() {
                theGroupesController.relecture();
            });

        };

        this.ajouteGroupe = function() {
            var nouveauGroupe = [ {
                nom : theGroupesController.nouveaugroupe.nom
            } ];
            $http.get("groupes.php", {
                params : {
                    itemNouveau : nouveauGroupe,
                    ajax : 1
                }
            }).success(function() {
                theGroupesController.relecture();
            });
        };

        this.relecture();
    } ]);

})() ;