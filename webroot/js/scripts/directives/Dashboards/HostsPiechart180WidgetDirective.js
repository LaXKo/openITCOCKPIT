angular.module('openITCOCKPIT').directive('hostsPiechart180Widget', function($http){
    return {
        restrict: 'E',
        templateUrl: '/dashboards/hostsPiechart180Widget.html',
        scope: {
            'widget': '='
        },

        controller: function($scope){

            // ITC-3037
            var $widgetContent = $('#widget-content-' + $scope.widget.id);
            $scope.readOnly    = parseInt($widgetContent.attr('data-readonly'));

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
