/* angularjs based javascript */
(function() {

    var app = angular.module('tableau', []);

    app.controller('TableauController', [ '$http', function($http) {
        this.locaux = tousLesLocaux.locaux;
        this.selectionLocal = null;
        this.locauxSelected = [];

        this.tousLesLocaux;

        var theTableauController = this;
        this.relecture = function() {
            $http.get("moteur.php", {
                params : {
                    action : "relecture"
                }
            }).success(function(data) {
                theTableauController.locaux = data.locaux;
                theTableauController.tousLesLocaux = [];
                theTableauController.locauxSelected = [];
                delete theTableauController.selectionLocal ;
            }).error(function(data) {
            });
        }

        this.localSelected = function() {
            return (this.locauxSelected.length > 0);
        }

        this.toggleSelectedLocal = function(localToSelect) {
            if (typeof localToSelect.selected === "undefined") {
                localToSelect.selected = true;
            } else {
                localToSelect.selected = !localToSelect.selected;
            }
            var currentNumberOfSelected = this.locauxSelected.length;
            var locauxSelected = [];
            for (key in this.locaux) {
                if (this.locaux[key].selected) locauxSelected.push(key);
            }
            this.locauxSelected = locauxSelected;
            if ((currentNumberOfSelected == 0) && (locauxSelected.length != 0)) {
                $http.get("moteur.php", {
                    params : {
                        action : "tousLesLocaux"
                    }
                }).success(function(data) {
                    theTableauController.tousLesLocaux = data.locaux;
                    delete theTableauController.selectionLocal ;
                });
            }

        }
    } ]);

})();