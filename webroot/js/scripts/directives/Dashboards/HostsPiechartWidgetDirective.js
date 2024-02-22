angular.module('openITCOCKPIT').directive('hostsPiechartWidget', function($http){
    return {
        restrict: 'E',
        templateUrl: '/dashboards/hostsPiechartWidget.html',
        scope: {
            'widget': '='
        },

        controller: function($scope){

            // ITC-3037
            $scope.readOnly    = $scope.widget.isReadonly;

            $scope.load = function(){
                $http.get("/angular/statuscount.json", {
                    params: {
                        'angular': true,
                        'recursive': true
                    }
                }).then(function(result){
                    $scope.hoststatusCount = result.data.hoststatusCount;
                    $scope.hoststatusCountPercentage = result.data.hoststatusCountPercentage;
                    $scope.init = false;
                });
            };

            $scope.load();

        },

        link: function($scope, element, attr){

        }
    };
});
