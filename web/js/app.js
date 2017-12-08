var app = angular.module("starting5",[]);

app.config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
})

app.controller('adminQuizz', [ '$scope', function($scope){
    $scope.title = "Admin quizz";
    $scope.quizz = {};
    $scope.quizz.type = "QCM";
    $scope.quizz.question = "";
}]);

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