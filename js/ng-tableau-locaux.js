(function(){
    var app = angular.module('tableau-locaux',[]) ;
    
    app.controller('LocauxController', [ '$http', function($http) {
        this.locaux;
        this.selectionLocal = null;
        this.locauxSelected = [];
        this.allSelected = false;
        this.nouveaulocal = "";

        var theLocauxController = this;

        this.relecture = function() {
            $http.get("moteur.php", {
                params : {
                    action : "tousLesLocaux"
                }
            }).success(function(data) {
                theLocauxController.locaux = data.locaux;
                theLocauxController.tousLesLocaux = [];
                theLocauxController.locauxSelected = [];
                theLocauxController.allSelected = false;
                theLocauxController.nouveaulocal.name = "";
                delete theLocauxController.selectionLocal;
            }).error(function(data) {
            });
        };

        this.editLocal = function(event, local) {
            /* clear any other edit field, save them if dirty */
            for (key in this.locaux) {
                if (this.locaux[key].editing) {
                    this.saveLocal(this.locaux[key]);
                }
            }
            local.editing = true;
        };

        this.stopEditLocal = function(local) {
            local.editing = false;
        };

        this.selectAll = function(all) {
            this.locauxSelected = [] ;
            for (key in this.locaux) {
                this.locaux[key].selected = all;
                if(all) {
                    this.locauxSelected.push(this.locaux[key].value) ;
                }
            }
            this.allSelected = all;
        };

        this.select = function(local) {            
            if (typeof local.selected == "undefined") {
                local.selected = true;
            } else {
                local.selected = !local.selected;
            }
            if(local.selected) {
                this.locauxSelected.push(local.value);
            } else {
                var index = this.locauxSelected.indexOf(local.value);
                if (index > -1) {
                    this.locauxSelected.splice(index, 1);
                }
            }
        };

        this.saveLocal = function(local) {
            local.editing = false;
            var item = {};
            item[local.value] = {
                nom : local.name
            };
            $http.get("locaux.php", {
                params : {
                    item : item,
                    ajax : 1
                }
            });
        };

        this.effaceLocaux = function() {
            var item = {};
            var localToDelete = {};
            for (key in this.locaux) {
                if (this.locaux[key].selected) {
                    var localToDelete = {
                        efface : 1
                    };
                    item[this.locaux[key].value] = localToDelete;
                }
            }
            $http.get("locaux.php", {
                params : {
                    item : item,
                    ajax : 1
                }
            }).success(function() {
                theLocauxController.relecture();
            });

        };

        this.ajouteLocal = function() {
            var nouveauLocal = [ {
                nom : theLocauxController.nouveaulocal.name
            } ];
            $http.get("locaux.php", {
                params : {
                    itemNouveau : nouveauLocal,
                    ajax : 1
                }
            }).success(function() {
                theLocauxController.relecture();
            });
        };

        this.relecture();
    } ]);

})() ;