angular.module('openITCOCKPIT').directive('modgearmanModule', function($http, $state, NotyService, RedirectService){
    return {
        restrict: 'E',
        templateUrl: '/ConfigurationFiles/ModGearmanModule.html',
        scope: {},

        controller: function($scope){

            $scope.post = {};

            $scope.init = true;
            $scope.load = function(){
                $http.get('/ConfigurationFiles/ModGearmanModule.json', {
                    params: {
                        'angular': true
                    }
                }).then(function(result){
                    $scope.post = result.data.config;
                    $scope.init = false;
                }, function errorCallback(result){
                    if(result.status === 403){
                        $state.go('403');
                    }

                    if(result.status === 404){
                        $state.go('404');
                    }
                });
            };

            $scope.submit = function(){
                $http.post('/ConfigurationFiles/ModGearmanModule.json?angular=true',
                    $scope.post
                ).then(function(result){
                    console.log('Data saved successfully');
                    NotyService.genericSuccess();
                    RedirectService.redirectWithFallback('ConfigurationFilesIndex');
                }, function errorCallback(result){
                    NotyService.genericError();
                    if(result.data.hasOwnProperty('error')){
                        $scope.errors = result.data.error;
                    }
                });
            };

            $scope.load();

        }

    };
});
