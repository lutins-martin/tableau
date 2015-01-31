(function() {
    var app = angular.module('tableau-messages', [ 'ui.bootstrap' ]);

    var readInMessages = function(lesMessages) {
        var reader = new commonmark.DocParser();
        var writer = new commonmark.HtmlRenderer();
        for (key in lesMessages) {
            var parsed = reader.parse(lesMessages[key].corps);
            lesMessages[key].corpsMarkedUp = writer.render(parsed);
        }
        return lesMessages;
    };

    app.controller('MessagesController', [ '$http', function($http) {
        var theMessageController = this;

        this.lesMessages = [];
        this.edited = null;
        this.nouveauMessage = {};
        this.messagesSelected = [];
        this.allSelected = false;

        this.datePickerFormat = 'yyyy-MM-dd';
        this.datePickerOptions = {
            formatYear : 'yy',
            startingDay : 1,
            showWeeks : 0
        };

        this.readInMessages = function(lesMessages) {
            var reader = new commonmark.DocParser();
            var writer = new commonmark.HtmlRenderer();
            lesMessages.length = 0;
            for (key in lesMessages) {
                var parsed = reader.parse(lesMessages[key].corps);
                lesMessages[key].corpsMarkedUp = writer.render(parsed);
                lesMessages.length++;
            }
            return lesMessages;
        };

        this.relecture = function() {
            $http.get("moteur.php", {
                params : {
                    action : "relectureMessages",
                    tous : 0
                }
            }).success(function(data) {
                theMessageController.lesMessages = readInMessages(data);
                theMessageController.edited = null;
                theMessageController.messagesSelected = [];
            });
        };

        this.openDatePicker = function($event, target) {
            $event.preventDefault();
            $event.stopPropagation();

            switch (target) {
                case "start":
                    this.edited.startDatePickerOpened = true;
                    break;
                case "end":
                    this.edited.endDatePickerOpened = true;
                    break;
            }
        };

        this.edit = function(message) {
            this.edited = message;
            this.messagesSelected = [];
            for (key in this.lesMessages) {
                this.lesMessages[key].selected = false;
            }
            this.allSelected = false;
        }

        this.clearMessage = function() {
            this.edited = null;
        }

        this.select = function(message) {
            this.edited = null;
            if (typeof message.selected == "undefined") {
                message.selected = true;
            } else {
                message.selected = !message.selected;
            }
            if (message.selected) {
                this.messagesSelected.push(message.id);
            } else {
                var index = this.messagesSelected.indexOf(message.id);
                if (index > -1) {
                    this.messagesSelected.splice(index, 1);
                }
            }
            var lengthOfLesMessages = 0;
            for (key in this.lesMessages) {
                lengthOfLesMessages++;
            }
            this.allSelected = (this.messagesSelected.length == lengthOfLesMessages);
        }

        this.selectAll = function(all) {
            this.messagesSelected = [];
            this.edited = null;
            for (key in this.lesMessages) {
                this.lesMessages[key].selected = all;
                if (all) {
                    this.messagesSelected.push(this.lesMessages[key].value);
                }
            }
            var lengthOfLesMessages = 0;
            for (key in this.lesMessages) {
                lengthOfLesMessages++;
            }
            this.allSelected = (this.messagesSelected.length == lengthOfLesMessages);
        }

        this.updateMessage = function() {

            $http.post("moteur.php", {
                action : "sauveMessage",
                ajax : 1,
                editedMessage : this.edited
            }).success(function() {
                theMessageController.relecture();
            })
        }

        this.effaceMessages = function() {
            if (confirm("On efface ces messages?")) {
                $http.post("moteur.php", {
                    action : "effaceMessages",
                    messageAEffacer : this.messagesSelected
                }).success(function() {
                    theMessageController.relecture();
                });
            }
        }

        this.relecture();
    } ]);

    app.directive('lesMessages', function() {
        return {
            restrict : 'E',
            templateUrl : 'lesMessages.html',
            controller : [ '$sce', function($sce) {
                this.trustAsHTML = function(contenu) {
                    return $sce.trustAsHtml(contenu);
                }

                this.tousLesMessages = readInMessages(this.tousLesMessages);

            } ],
            controllerAs : "messages"
        };
    });

})();