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
    return {
        getRandomQuizz: function () {
            return $http.post("/quizz/getRandomQuizz", {responseType: "json"});
        }
    }
});

app.controller('Quizz', [ '$scope', '$http', 'ServiceQuizz' , function($scope, $http, ServiceQuizz){
    $scope.started = false;
    $scope.loadingQuizz = false;

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


}]);