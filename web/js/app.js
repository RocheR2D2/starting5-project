var app = angular.module("starting5",['ngDragDrop']);

app.config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
})

var base_url = window.location.origin;
if(window.location.href.indexOf("app_dev.php") > -1){
    base_url += "/app_dev.php";
}

/* ### ADMIN QUIZZ ### */

app.controller('adminQuizz', [ '$scope', function($scope){
    $scope.title = "Admin quizz";
    $scope.quizz = {};
    $scope.quizz.type = "QCM";
    $scope.quizz.question = "";
}]);


/* ### QUIZZ ### */

app.factory("ServiceQuizz", function ($http) {
        var getRandomQuizz = function () {
            return $http.post( base_url + "/quizz/getRandomQuizz", {responseType: "json"});
        };

        var validateQuizz = function ($answer) {
            return $http.post( base_url + "/quizz/validateQuizz", {"answer":$answer});
        };

    return {
        getRandomQuizz: getRandomQuizz,
        validateQuizz: validateQuizz
    };

});

app.controller('Quizz', [ '$scope', '$http', 'ServiceQuizz' , function($scope, $http, ServiceQuizz){
    $scope.started = false;
    $scope.loadingQuizz = false;
    $scope.selectedQCM = 0;
    $scope.validatingQuizz = false;
    $scope.validQuizz = false;
    $scope.quizzEnd = false;

    $scope.allquizz = [];
    $scope.step = 0;

    $scope.countDown = 10;
    var timer;

    $scope.startTimer = function(){
        clearInterval(timer);
        $scope.countDown = 10;
        timer = setInterval(function(){
            $scope.countDown--;
            if($scope.countDown < 0){
                $scope.next();
            }
            $scope.$apply();
        }, 1000);
    }


    $scope.startQuizz = function(){
        $scope.started = true;
        $scope.loadingQuizz = true;
        ServiceQuizz.getRandomQuizz().then(function (res) {
            $scope.allquizz = res.data;
            $scope.loadingQuizz = false;
            $scope.startTimer();
        }, function (err) {
            console.log(err);
        });
    }

    $scope.next = function(){
        $scope.step++;

        if($scope.step == $scope.allquizz.length){
            $scope.validate();
        }else{
            $scope.startTimer();
        }
    }


    $scope.validate = function(){
        $scope.validatingQuizz = true;

        ServiceQuizz.validateQuizz($scope.allquizz).then(function (res) {
            $scope.validatingQuizz = false;
            $scope.quizzEnd = true;
            $scope.resultats = res.data[0];
            $scope.totalPts = res.data[1];

            var total = $("#user_points").text();
            total = parseInt(total) + $scope.totalPts;
            $("#user_points").text(total);

        }, function (err) {
            console.log(err);
        });
    }

    $scope.bindSelectedQCM = function(quizz, newVal){
        quizz.QCMAnswer = newVal;
    }

}]);

/* ### CREATE FIVE TEAM ### */

app.factory("ServiceFive", function ($http) {
    var getPlayer = function (route) {
        if(route == "public"){
            return $http.get(base_url + "/public/getPlayers", {responseType: "json"});
        }else{
            return $http.get(base_url + "/team/getPlayers", {responseType: "json"});
        }
    };

    var sendTeam = function(team, route){
        if(route == "edit"){
            return $http.post(base_url + "/team/editTeam", team);
        }else if(route == "public"){
            return $http.post(base_url + "/team/createPublicTeam", team);
        }else{
            return $http.post(base_url + "/team/createTeam", team);
        }
    };

    var getStadiums = function(route){
        if(route == "public"){
            return $http.get(base_url + "/public/getStadiums", {responseType: "json"});
        }else{
            return $http.get(base_url + "/json/myStadiums", {responseType: "json"});
        }
    };

    var getTrainers = function(route){
        if(route == "public"){
            return $http.get(base_url + "/public/getTrainers", {responseType: "json"});
        }else{
            return $http.get(base_url + "/json/myTrainers", {responseType: "json"});
        }
    };

    var getInfosEdit = function (id){
        return $http.get(base_url + "/json/" + id + "/myTeam/", {responseType: "json"});
    }

    return {
        getPlayer: getPlayer,
        sendTeam: sendTeam,
        getStadiums: getStadiums,
        getTrainers: getTrainers,
        getInfosEdit: getInfosEdit
    };

});


app.controller('Five', [ '$scope', 'ServiceFive', '$timeout', '$filter', function($scope, ServiceFive, $timeout, $filter){

    var route = window.location.pathname;

    if(route.indexOf("edit") > -1){
        var teamId = parseInt(route.split('/team/')[1].split('/edit')[0]);
        route = "edit";
        $scope.route = route;
    }else if(route.indexOf("my-five") > -1){
        route = "public";
        $scope.route = route;
    }

    $scope.playerSearch = "";

    $scope.filterIt = function() {
        return $filter('filter')($scope.players, $scope.playerSearch);
    };

    $scope.clearplayerSearch = function(){
        $scope.playerSearch = "";
        $scope.playersLimit = 20;
    }

    $scope.playersLimit = 20;

    //InfiniteScroll
    $scope.loadMore = function() {
        if($scope.playersLimit <= $scope.players.length) {
            if ($scope.playersLimit + 20 < $scope.players.length) {
                $scope.playersLimit += 20;
            } else {
                $scope.playersLimit = $scope.players.length;
            }
        }
    };

    // Each time the user scrolls
    $(".container-players").scroll(function() {
        // End of the document reached?
        if ($(".container-players").scrollTop() + $(".container-players").height() + 20 > $(".list-player").scrollTop() + $(".list-player").height()) {
            $scope.loadMore();
            $scope.$digest();
        }
    });


    $scope.center = {};
    $scope.smallForward = {};
    $scope.powerForward = {};
    $scope.shootingGuard = {};
    $scope.pointGuard = {};

    $scope.players = {};
    $scope.loadingPlayers = true;

    $scope.selectedPlayer = {};
    $scope.selectedPoste = '';

    $scope.sendingTeam = false;

    $scope.trainers = [];
    $scope.trainer = null;
    $scope.loadingTrainers = false;
    $scope.stadiums = [];
    $scope.stadium = null;
    $scope.loadingStadiums = false;

    $scope.teamName = "";
    $scope.username = "";

    $scope.editTeamId = null;

    $scope.sendingDone = false;

    getPlayers();

    $scope.getPlayers = getPlayers();

    function getPlayers(){

        ServiceFive.getPlayer($scope.route).then(function(res){
            $scope.players = res.data;
            $scope.loadingPlayers = false;
            if(route == "edit"){
                getInfosEdit();
            }
            $timeout(function(){
                var width = $(".container-players").width();
                $(".sendTeam").css("width", width);
                })

        }, function(err){
            console.log(err);
        })

    }

    function getInfosEdit(){
        ServiceFive.getInfosEdit(teamId).then(function(res){

                $scope.center = res.data.center;
                $scope.smallForward = res.data.smallForward;
                $scope.powerForward = res.data.powerForward;
                $scope.shootingGuard = res.data.shootingGuard;
                $scope.pointGuard = res.data.pointGuard;



                for(var i=0;i<$scope.players.length;i++){
                    if($scope.players[i].id == $scope.center.id || $scope.players[i].id == $scope.smallForward.id || $scope.players[i].id == $scope.powerForward.id
                    || $scope.players[i].id == $scope.shootingGuard.id || $scope.players[i].id == $scope.pointGuard.id){
                        $scope.players.splice(i,1);
                        if(i>0){
                            i--;
                        }

                    }else{

                    }
                }

                $scope.editTeamId = res.data.id;

            $scope.teamName = res.data.name;
            $scope.stadium = res.data.stadiumId;
                $scope.trainer = res.data.trainerId;

        })
    }

    $scope.clearcenter = function(){
        $scope.players.push($scope.center);
        $scope.center = {};
    }

    $scope.clearsmallForward = function(){
        $scope.players.push($scope.smallForward);
        $scope.smallForward = {};
    }

    $scope.clearpowerForward = function(){
        $scope.players.push($scope.powerForward);
        $scope.powerForward = {};
    }

    $scope.clearshootingGuard = function(){
        $scope.players.push($scope.shootingGuard);
        $scope.shootingGuard = {};
    }

    $scope.clearpointGuard = function(){
        $scope.players.push($scope.pointGuard);
        $scope.pointGuard = {};
    }

    $scope.dropCallback = function (evt, ui) {
        // the model
        var obj = ui.draggable.scope().dndDragItem;

        for(var j=0;j<$scope.players.length;j++){
            if($scope.players[j].playerId == obj.playerId){
                $scope.players.splice(j,1);
                return false;
            }
        }
    };

    $scope.dragCallback = function(evt, ui, player){
        $scope.selectedPlayer = player;
        $scope.selectedPoste = player.position;
        $scope.$apply();
    }

    $scope.dragStopCallback = function(){
        $scope.selectedPlayer = {};
        $scope.selectedPoste = "";
        $scope.$apply();
    }

    $scope.sendTeam = function(){
        if(!$scope.center.fullName || !$scope.smallForward.fullName || !$scope.powerForward.fullName || !$scope.shootingGuard.fullName || !$scope.pointGuard.fullName){
            return false;
        }

        if($scope.trainers.length <= 0){
            $scope.loadingTrainers = true;
            ServiceFive.getTrainers($scope.route)
                .then(function(response){
                    $scope.trainers = [];
                    $scope.loadingTrainers = false;
                    if($scope.route == 'public'){
                        $scope.trainers = response.data;
                    }else{
                        for(var i=0;i<response.data.length;i++){
                            $scope.trainers.push(response.data[i].trainerId);
                        }
                    }
                    if(route != "edit"){
                        $scope.trainer = $scope.trainers[0];
                    }
                }, function(error){
                    console.log(error);
                });
        }

    if($scope.stadiums.length <= 0){
        $scope.loadingStadiums = true;
        ServiceFive.getStadiums($scope.route)
            .then(function(response){
                $scope.stadiums = [];
                $scope.loadingStadiums = false;
                if($scope.route == 'public'){
                    $scope.stadiums = response.data;
                }else{
                    for(var i=0;i<response.data.length;i++){
                        $scope.stadiums.push(response.data[i].stadiumId);
                    }
                }
                if(route != "edit"){
                    $scope.stadium = $scope.stadiums[0];
                }
            }, function(error){
                console.log(error);
            });
    }


        $("#createTeam").modal("show");
    };

    $scope.createTeam = function(){
        $scope.sendingTeam = true;

        var players = {
            "center": $scope.center,
            "smallForward": $scope.smallForward,
            "powerForward": $scope.powerForward,
            "shootingGuard": $scope.shootingGuard,
            "pointGuard": $scope.pointGuard
        };

        var team = {
            "teamName" : $scope.teamName,
            "players" : players,
            "stadium" : $scope.stadium,
            "trainer" : $scope.trainer
        };

        if(route == 'public'){
            team.username = $scope.username;
        }

        if(route == 'edit'){
            team.id = $scope.editTeamId;
        }


        ServiceFive.sendTeam(team, route).then(function(res){
            $scope.sendingTeam = false;

            //reset players
                $scope.center = null;
                $scope.smallForward =null;
                $scope.powerForward =null;
                $scope.shootingGuard =null;
                $scope.pointGuard =null;
                $scope.teamName = null;
                $scope.trainer = null;
                $scope.stadium = null;

                $scope.sendingDone = true;
                $("#createTeam").modal("hide");

            if(route == "public"){
                window.location.href = base_url + "/public/teams";
            }else{
                window.location.href= base_url + "/my-teams";
            }




        }, function(err){
            console.log(err);
        })
    }

    window.onresize = resizeBtn;

    function resizeBtn(){
        if(window.innerWidth > 1250){
            var width = $(".container-players").width();
            $(".sendTeam").css("width", width);
        }
    }

}]);


/* ### BATTLE MODE ### */

app.factory("ServiceBattle", function ($http) {
    var getPlayer = function (battleId,roundId) {
        return $http.post(base_url + "/json/myBattlePlayers/" + battleId + "/" + roundId, {responseType: "json"});
    };

    var sendTeam = function(pathSubmit,data){
        return $http.post(pathSubmit, data);
    }

    return {
        getPlayer: getPlayer,
        sendTeam: sendTeam
    };

});


app.controller('Battle', [ '$scope', 'ServiceBattle', '$timeout', function($scope, ServiceBattle, $timeout){


    $scope.player1 = {};
    $scope.player2 = {};
    $scope.player3 = {};


    $scope.players = {};
    $scope.loadingPlayers = true;

    $scope.selectedPlayer = {};
    $scope.selectedPoste = '';

    $scope.playType = 0;
    $scope.battleId = 0;
    $scope.roundId = 0;

    $scope.sendingTeam = false;

    $scope.sendingDone = false;

    getPlayers();

    function getPlayers(){

        var route = window.location.pathname;
        var url = route.split("/battle/")[1];
        url = url.split("/play/");

        var battleId = url[0];
        var roundId = url[1];

        if(!battleId || !roundId){
            return false;
        }

        $scope.battleId = battleId;
        $scope.roundId = roundId;

        ServiceBattle.getPlayer(battleId,roundId).then(function(res){

            $scope.playType = res.data[0];
            var players = res.data[1];
            var filterPlayers = [];

            //Filter to get only players stats with action point
            for(var i =0;i<players.length;i++){

                players[i].playerId.actionPoint = players[i].actionPoint;

                filterPlayers.push(players[i].playerId);
            }

            $scope.players = filterPlayers;

            $scope.loadingPlayers = false;
            $timeout(function(){
                var width = $(".container-players").width();
                $(".sendTeam").css("width", width);
            })

        }, function(err){
            console.log(err);
        })

    }


    $scope.clearplayer3 = function(){
        $scope.players.push($scope.player3);
        $scope.player3 = {};
    }

    $scope.clearplayer1 = function(){
        $scope.players.push($scope.player1);
        $scope.player1 = {};
    }

    $scope.clearplayer2 = function(){
        $scope.players.push($scope.player2);
        $scope.player2 = {};
    }


    $scope.dropCallback = function (evt, ui) {
        // the model
        var obj = ui.draggable.scope().dndDragItem;

        for(var j=0;j<$scope.players.length;j++){
            if($scope.players[j].playerId == obj.playerId){
                $scope.players.splice(j,1);
                return false;
            }
        }
    };

    $scope.dragCallback = function(evt, ui, player){
        $scope.selectedPlayer = player;
        $scope.selectedPoste = player.position;
        $scope.$apply();
    }

    $scope.dragStopCallback = function(){
        $scope.selectedPlayer = {};
        $scope.selectedPoste = "";
        $scope.$apply();
    }

    $scope.sendTeam = function(){

        $scope.sendingTeam = true;

        var data;

        switch($scope.playType){
            case 1 :
                data = {
                    'players' : [$scope.player1.playerId],
                    'playType': $scope.playType,
                    'roundId': $scope.roundId,
                    'battleId': $scope.battleId,
                    'isCritical' : false
                };
                break;
            case 2:
                data = {
                    'players': [$scope.player1.playerId, $scope.player2.playerId],
                    'playType': $scope.playType,
                    'roundId': $scope.roundId,
                    'battleId': $scope.battleId,
                    'isCritical' : false
                };
                break;
            case 3:
                data = {
                    'players': [$scope.player1.playerId, $scope.player2.playerId, $scope.player3.playerId],
                    'playType': $scope.playType,
                    'roundId': $scope.roundId,
                    'battleId': $scope.battleId,
                    'isCritical' : false
                };
                break;
        }


        var pathSubmit = base_url + '/battle/new/play';

        ServiceBattle.sendTeam(pathSubmit,data)
            .then(function(response){
                $scope.sendingTeam = false;
                $scope.sendingDone = true;
                window.location = base_url + "/battle/" + $scope.battleId + "/played/" + $scope.roundId;
            },
            function(error){
                console.log(error);
            })
    }

    window.onresize = resizeBtn;

    function resizeBtn() {
        if (window.innerWidth > 1250) {
            var width = $(".container-players").width();
            $(".sendTeam").css("width", width);
        }
    }

}]);


/*
 Template Name: Upcube - Bootstrap 4 Admin Dashboard
 Author: Themesdesign
 Website: www.themesdesign.in
 File: Main js
 */

!function(e){"use strict";var t=function(){this.$body=e("body"),this.$wrapper=e("#wrapper"),this.$btnFullScreen=e("#btn-fullscreen"),this.$leftMenuButton=e(".button-menu-mobile"),this.$menuItem=e(".has_sub > a")};t.prototype.initSlimscroll=function(){e(".slimscrollleft").slimscroll({height:"auto",position:"right",size:"10px",color:"#9ea5ab"})},t.prototype.initLeftMenuCollapse=function(){var e=this;this.$leftMenuButton.on("click",function(t){t.preventDefault(),e.$body.toggleClass("fixed-left-void"),e.$wrapper.toggleClass("enlarged")})},t.prototype.initComponents=function(){e('[data-toggle="tooltip"]').tooltip(),e('[data-toggle="popover"]').popover()},t.prototype.initFullScreen=function(){var e=this;e.$btnFullScreen.on("click",function(e){e.preventDefault(),document.fullscreenElement||document.mozFullScreenElement||document.webkitFullscreenElement?document.cancelFullScreen?document.cancelFullScreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitCancelFullScreen&&document.webkitCancelFullScreen():document.documentElement.requestFullscreen?document.documentElement.requestFullscreen():document.documentElement.mozRequestFullScreen?document.documentElement.mozRequestFullScreen():document.documentElement.webkitRequestFullscreen&&document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)})},t.prototype.initMenu=function(){function t(){e(".has_sub").each(function(){var t=e(this);t.hasClass("nav-active")&&t.find("> ul").slideUp(300,function(){t.removeClass("nav-active")})})}function n(){var t=e(document).height();t>e(".body-content").height()&&e(".body-content").height(t)}var o=this;o.$menuItem.on("click",function(){var i=e(this).parent(),l=i.find("> ul");return o.$body.hasClass("sidebar-collapsed")||(l.is(":visible")?l.slideUp(300,function(){i.removeClass("nav-active"),e(".body-content").css({height:""}),n()}):(t(),i.addClass("nav-active"),l.slideDown(300,function(){n()}))),!1})},t.prototype.activateMenuItem=function(){e("#sidebar-menu a").each(function(){this.href==window.location.href&&(e(this).addClass("active"),e(this).parent().addClass("active"),e(this).parent().parent().prev().addClass("active"),e(this).parent().parent().parent().addClass("active"),e(this).parent().parent().prev().click())})},t.prototype.Preloader=function(){e(window).load(function(){e("#status").fadeOut(),e("#preloader").delay(350).fadeOut("slow"),e("body").delay(350).css({overflow:"visible"})})},t.prototype.init=function(){this.initSlimscroll(),this.initLeftMenuCollapse(),this.initComponents(),this.initFullScreen(),this.initMenu(),this.activateMenuItem(),this.Preloader()},e.MainApp=new t,e.MainApp.Constructor=t}(window.jQuery),function(e){"use strict";e.MainApp.init()}(window.jQuery);
