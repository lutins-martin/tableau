/* angularjs based javascript */
(function() {

    var app = angular.module('tableau', [ 'ngRoute', 'tableau-locaux', 'tableau-groupes', 'tableau-messages',
            'tableau-educatrices', 'tableau-backgrounds' ]);

    app.config([ '$routeProvider', function($routeProvider) {
        $routeProvider.when('/tableau/', {
            templateUrl : 'tableau.html',
            controller : 'TableauController',
            controllerAs : 'tableau'
        }).when('/locaux/', {
            templateUrl : 'locaux.html',
            controller : 'LocauxController',
            controllerAs : 'locaux'
        }).when('/groupes/', {
            templateUrl : 'groupes.html',
            controller : 'GroupesController',
            controllerAs : 'groupes'
        }).when('/educatrices', {
            templateUrl : 'educatrices.html',
            controller : 'EducatricesController',
            controllerAs : 'educatrices'
        }).when('/messages/', {
            templateUrl : 'messages.html',
            controller : 'MessagesController',
            controllerAs : 'messages'
        }).when('/arriereplans/', {
            templateUrl : 'arriereplans.html',
            controller : 'BackgroundController',
            controllerAs : 'backgroundCtrl'
        }).otherwise({
            redirectTo : '/tableau/'
        });
    } ]);

    app.controller('TableauController', [ '$http', '$timeout', function($http, $timeout) {
        this.locaux = tousLesLocaux.locaux;
        this.selectionLocal = null;
        this.locauxSelected = [];
        this.allSelected = false;
        this.dernierChangement = 0;

        this.tousLesLocaux;

        var theTableauController = this;
        this.relecture = function() {
            $http.get("moteur.php", {
                params : {
                    action : "relecture"
                }
            }).success(function(data) {
                if (theTableauController.locauxSelected.length == 0) {
                    theTableauController.locaux = data.locaux;
                    theTableauController.tousLesLocaux = [];
                    theTableauController.locauxSelected = [];
                    theTableauController.allSelected = false;
                    delete theTableauController.selectionLocal;
                    if (theTableauController.dernierChangement < data.dernierChangement) {
                        theTableauController.dernierChangement = data.dernierChangement;
                        theTableauController.updateBackground();

                    }
                }
            }).error(function(data) {
            });
        };

        this.updateBackground = function() {
            $http.get("moteur.php", {
                params : {
                    action : "getBackgroundImageFileName"
                }
            }).success(function(data) {
                $('html').css('backgroundImage', 'url(' + data.file + ')');
            });
        }

        this.updateLocaux = function() {
            var local = {};
            this.locauxSelected.forEach(function(educatriceIndex) {
                local[educatriceIndex] = theTableauController.selectionLocal;
            });
            $http.post("moteur.php", {
                action : "changerLocal",
                local : local
            }).success(function() {
                theTableauController.locauxSelected = [];
                theTableauController.relecture();
            });
        };

        this.selectAll = function(selectAll) {
            this.locauxSelected = [];
            this.allSelected = selectAll;
            for (index in this.locaux) {
                this.locaux[index].selected = selectAll;
                if (selectAll) this.locauxSelected.push(index);
            }
            if (selectAll) this.loadAllLocaux();
        };

        this.localSelected = function() {
            return (this.locauxSelected.length > 0);
        };

        this.loadAllLocaux = function() {
            $http.get("moteur.php", {
                params : {
                    action : "tousLesLocaux"
                }
            }).success(function(data) {
                theTableauController.tousLesLocaux = data.locaux;
                delete theTableauController.selectionLocal;
            });
        };

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
            if ((currentNumberOfSelected == 0) && (locauxSelected.length != 0)) this.loadAllLocaux();

        };

        this.miseAJourPeriodique = function() {
            if (theTableauController.locauxSelected.length == 0) {
                theTableauController.relecture();
                theTableauController.relectureMessages();
            }
            $timeout(function() {
                theTableauController.miseAJourPeriodique();
            }, 30123);
        }

        $timeout(function() {
            theTableauController.miseAJourPeriodique();
        }, 30123);
    } ]);

    app.directive('heureEtDate', function($timeout) {
        return {
            restrict : 'E',
            templateUrl : 'heureEtDate.html',
            controller : function() {
                this.heure;
                this.jourDeLaSemaine;

                var horlogeController = this;
                this.marcher = function() {
                    var currentTimeAndDate = new Date();
                    var minutes = ('0' + currentTimeAndDate.getMinutes()).slice(-2);
                    this.heure = currentTimeAndDate.getHours() + ":" + minutes;
                    this.jourDeLaSemaine = currentTimeAndDate.getDateEnFrancais();

                    var nextMinutes = new Date(currentTimeAndDate.getFullYear(), currentTimeAndDate.getMonth(), currentTimeAndDate
                            .getDate(), currentTimeAndDate.getHours(), currentTimeAndDate.getMinutes() + 1, 00);

                    var expiration = nextMinutes.valueOf() - currentTimeAndDate.valueOf();
                    $timeout(function() {
                        horlogeController.marcher();
                    }, expiration);
                };

                this.marcher();
            },
            controllerAs : 'horloge'

        };
    });

    app.filter('trusted', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);
        }
    });
})();

Date.prototype.getDateEnFrancais = function() {
    var jourSemaine = Array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
    var moisDeLannee = Array("janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre",
            "novembre", "décembre");

    var jour = this.getDay();
    var mois = this.getMonth();

    return jourSemaine[jour] + " " + this.getDate().toString() + " " + moisDeLannee[mois] + " " + this.getFullYear();
};