/* angularjs based javascript */
(function() {

    var app = angular.module('tableau', [ 'ngRoute' ]);

    app.config([ '$routeProvider', function($routeProvider) {
        $routeProvider.when('/tableau', {
            templateUrl : 'tableau.html',
            controller : 'TableauController'
        }).when('/locaux', {
            templateUrl : 'locaux.html',
            controller : 'LocauxController'
        }).when('/groupes', {
            templateUrl : 'groupes.html',
            controller : 'GroupesController'
        }).when('/educatrices', {
            templateUrl : 'educatrices.html',
            controller : 'EducatricesController'
        }).when('/messages', {
            templateUrl : 'messages.html',
            controller : 'MessagesController'
        }).when('/arriereplans', {
            templateUrl : 'arriere-plans.html',
            controller : 'ArrierePlansController'
        }).otherwise({
            redirectTo : '/tableau'
        });
    } ]);

    app.controller('TableauController', [ '$http', '$timeout', function($http, $timeout) {
        this.locaux = tousLesLocaux.locaux;
        this.selectionLocal = null;
        this.locauxSelected = [];
        this.allSelected = false;

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
                theTableauController.allSelected = false;
                delete theTableauController.selectionLocal;
            }).error(function(data) {
            });
        };

        this.updateLocaux = function() {
            var local = {};
            this.locauxSelected.forEach(function(educatriceIndex) {
                local[educatriceIndex] = theTableauController.selectionLocal;
            });
            $http.post("moteur.php", {
                action : "changerLocal",
                local : local
            }).success(function() {
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
            if (theTableauController.locauxSelected.count() == 0) {
                theTableauController.relecture();
            }
            $timeout(function() {
                theTableauController.miseAJourPeriodique, 300
            })
        }

        $timeout(function() {
            theTableauController.miseAJourPeriodique, 300
        });
    } ]);

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
            for (key in this.locaux) {
                this.locaux[key].selected = all;
            }
            this.allSelected = all;
        };

        this.select = function(local) {
            if (typeof local.selected == "undefined") {
                local.selected = true;
            } else {
                local.selected = !local.selected;
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

        this.ajouteLocal = function() {
            var nouveauLocal = [{
                nom : theLocauxController.nouveaulocal.name
            }];
            $http.get("locaux.php", {
                params : {
                    itemNouveau : nouveauLocal,
                    ajax: 1
                }
            }).success(function() {
                theLocauxController.relecture();
            });
        };

        this.relecture();
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
                    this.jourDeLaSemaine = currentTimeAndDate.toLocaleDateString('fr-CA', {
                        weekday : "long",
                        year : "numeric",
                        month : "long",
                        day : "numeric"
                    });

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

    app.filter('trusted', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);
        }
    });
})();