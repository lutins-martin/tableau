(function() {
    var app = angular.module('tableau-educatrices', []);

    app.controller('EducatricesController', [ '$http', function($http) {

        this.lesEducatrices = {};
        this.allSelected = false;
        this.selectedEducatrices = [];
        this.tousLesGroupes = [];

        var theEducatricesController = this;

        this.relecture = function() {
            $http.get("moteur.php", {
                params : {
                    action : 'toutesLesEducatricesAvecGroupes'
                }
            }).success(function(data) {
                theEducatricesController.lesEducatrices = data.educatrices;
                theEducatricesController.selectedEducatrices = [];
                theEducatricesController.nouvelleEducatrice = {} ;
            }).error(function() {

            });
        };

        this.select = function(educatrice) {
            if (typeof educatrice.selected == 'undefined') {
                educatrice.selected = true;
            } else {
                educatrice.selected = !educatrice.selected;
            }
            if (educatrice.selected) {
                this.selectedEducatrices.push(educatrice.valeur);
            } else {
                var index = this.selectedEducatrices.indexOf(educatrice.valeur);
                if (index > -1) {
                    this.selectedEducatrices.splice(index, 1);
                }
            }
            this.allSelected = (this.selectedEducatrices.length == this.lesEducatrices.length);
        }

        this.selectAll = function(all) {
            this.allSelected = all;
            if (all) {
                for (index in this.lesEducatrices) {
                    this.selectedEducatrices.push(this.lesEducatrices[index].valeur);
                    this.lesEducatrices[index].selected = true;
                }
            } else {
                this.selectedEducatrices = [];
                for (index in this.lesEducatrices) {
                    this.lesEducatrices[index].selected = false;
                }
            }
        }

        this.groupeEdit = function(educatrice) {
            for (index in this.lesEducatrices) {
                this.lesEducatrices[index].groupe.inEditMode = false;
            }
            if (educatrice != null) {
                educatrice.groupe.inEditMode = true;
                this.getTousLesGroupes();
            }
        }

        this.getTousLesGroupes = function() {
            $http.get("moteur.php", {
                params : {
                    action : "tousLesGroupes"
                }
            }).success(function(data) {
                theEducatricesController.tousLesGroupes = data.groupes;
            }).error(function(data) {
            });
        }

        this.updateGroupe = function(educatrice) {
            var item = {};
            item[educatrice.valeur] = {
                groupe : educatrice.groupe.valeur
            };
            $http.get("educatrices.php", {
                params : {
                    item : item,
                    ajax : 1
                }
            }).success(function(data) {
                theEducatricesController.relecture();
            });
        }

        this.getTousLesGroupesSiVides = function() {
            if (this.tousLesGroupes.length == 0) {
                this.getTousLesGroupes();
            }
        }

        this.effaceEducatrices = function() {
            var message = "Effacer ces éducatrices:";
            for (index in this.lesEducatrices) {
                if (this.lesEducatrices[index].selected) {
                    message = message + " " + this.lesEducatrices[index].nom;
                }
            }
            if (confirm(message)) {
                var item = {};
                for (index in this.selectedEducatrices) {
                    item[this.selectedEducatrices[index]] = {
                        efface : 1
                    };
                }
                $http.get("educatrices.php", {
                    params : {
                        item : item,
                        ajax : 1
                    }
                }).success(function(data) {
                    theEducatricesController.relecture();
                });
            } else {
                this.relecture();
            }
        }

        this.ajouteEducatrice = function() {
            var itemNouveau = this.nouvelleEducatrice;
            $http.get("educatrices.php", {
                params : {
                    itemNouveau : itemNouveau,
                    ajax : 1
                }
            }).success(function(data) {
                theEducatricesController.relecture();
            })
        }

        this.relecture();
    } ]);
})();