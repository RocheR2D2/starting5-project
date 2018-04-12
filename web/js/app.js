var app = angular.module("starting5",['ngDragDrop']);

app.config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
})

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
            return $http.post("/app_dev.php/quizz/getRandomQuizz", {responseType: "json"});
        };

        var validateQuizz = function ($id,$answer) {
            return $http.post("/app_dev.php/quizz/validateQuizz", {"id":$id,"answer":$answer});
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

    $scope.startQuizz = function(){
        $scope.started = true;
        $scope.loadingQuizz = true;
        ServiceQuizz.getRandomQuizz().then(function (res) {
            $scope.quizz = res.data;
            $scope.loadingQuizz = false;
        }, function (err) {
            console.log(err);
        });
    }

    $scope.validate = function(){
        var res = null;
        $scope.validatingQuizz = true;
        if($scope.quizz.type == 'QCM'){
            res = $scope.selectedQCM;
        }else if($scope.quizz.type == 'Question'){
            res = $scope.quizz.QuestionAnswer;
        }
        ServiceQuizz.validateQuizz($scope.quizz.id, res).then(function (res) {
            $scope.validQuizz = (res.data == 'true' ? res.data = true : res.data = false);
            $scope.validatingQuizz = false;
            $scope.quizzEnd = true;
        }, function (err) {
            console.log(err);
        });
    }

    $scope.bindSelectedQCM = function(newVal){
        $scope.selectedQCM = newVal;
    }

}]);

/* ### CREATE FIVE TEAM ### */

app.factory("ServiceFive", function ($http) {
    var getPlayer = function () {
        return $http.get("/app_dev.php/team/getPlayers", {responseType: "json"});
    };

    return {
        getPlayer: getPlayer
    };

});

app.controller('Five', [ '$scope', 'ServiceFive', '$timeout', function($scope, ServiceFive, $timeout){

    $scope.player1 = {};
    $scope.player2 = {};
    $scope.player3 = {};
    $scope.player4 = {};
    $scope.player5 = {};

    $scope.players = [];
    $scope.loadingPlayers = true;

    $scope.selectedPlayer = {};
    $scope.selectedPoste = '';

    getPlayers();

    $scope.getPlayers = getPlayers();

    function getPlayers(){

        ServiceFive.getPlayer().then(function(res){
            $scope.players = res.data;
            $scope.loadingPlayers = false;
        }, function(err){
            console.log(err);
        })

    }

    $scope.clearPlayer1 = function(){
        $scope.players.push($scope.player1);
        $scope.player1 = {};
    }

    $scope.dropCallback = function (evt, ui) {
        // the model
        var obj = ui.draggable.scope().dndDragItem;

        console.log(obj);

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

}]);