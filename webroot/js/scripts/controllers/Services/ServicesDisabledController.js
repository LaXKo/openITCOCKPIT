angular.module('openITCOCKPIT')
    .controller('ServicesDisabledController', function($scope, $http, $rootScope, $httpParamSerializer, SortService, MassChangeService, QueryStringService){
        $rootScope.lastObjectName = null;

        SortService.setSort(QueryStringService.getValue('sort', 'Hosts.name'));
        SortService.setDirection(QueryStringService.getValue('direction', 'asc'));
        $scope.currentPage = 1;

        //There is no service status for not monitored services :)
        $scope.fakeServicestatus = {
            Servicestatus: {
                currentState: 5
            }
        };

        /*** Filter Settings ***/
        var defaultFilter = function(){
            $scope.filter = {
                Hosts: {
                    name: '',
                    name_regex: false
                },
                Services: {
                    name: '',
                    name_regex: false
                }
            };
        };
        /*** Filter end ***/
        $scope.massChange = {};
        $scope.selectedElements = 0;
        $scope.deleteUrl = '/services/delete/';
        $scope.activateUrl = '/services/enable/';


        $scope.init = true;
        $scope.showFilter = false;

        var forTemplate = function(serverResponse){
            // Create a list of host with all services

            var hostWithServices = [];

            var arrayIndexOfHostId = {};

            for(var i in serverResponse){
                var hostId = serverResponse[i].Host.id;

                var index = null;

                if(!arrayIndexOfHostId.hasOwnProperty(hostId)){
                    //We need to use an array [] because an hash map {} has no fixed order.
                    index = hostWithServices.length; // length is automaticaly the next index :)
                    arrayIndexOfHostId[hostId] = index;

                    hostWithServices.push({
                        Host: serverResponse[i].Host,
                        Hoststatus: serverResponse[i].Hoststatus,
                        Services: []
                    });
                }

                index = arrayIndexOfHostId[hostId];

                hostWithServices[index].Services.push(
                    serverResponse[i].Service
                );
            }

            return hostWithServices;
        };


        $scope.load = function(){

            var params = {
                'angular': true,
                'sort': SortService.getSort(),
                'page': $scope.currentPage,
                'direction': SortService.getDirection(),
                'filter[Hosts.name]': $scope.filter.Hosts.name,
                'filter[Hosts.name_regex]': $scope.filter.Hosts.name_regex,
                'filter[servicename]': $scope.filter.Services.name,
                'filter[servicename_regex]': $scope.filter.Services.name_regex
            };


            $http.get("/services/disabled.json", {
                params: params
            }).then(function(result){
                $scope.services = [];
                $scope.serverResult = result.data.all_services;
                $scope.services = forTemplate(result.data.all_services);
                $scope.paging = result.data.paging;
                $scope.init = false;
            });
        };

        $scope.triggerFilter = function(){
            $scope.showFilter = !$scope.showFilter === true;
        };

        $scope.resetFilter = function(){
            defaultFilter();
            $scope.undoSelection();
        };

        $scope.selectAll = function(){
            if($scope.services){
                for(var key in $scope.serverResult){
                    if($scope.serverResult[key].Service.allow_edit){
                        var id = $scope.serverResult[key].Service.id;
                        $scope.massChange[id] = true;
                    }
                }
            }
        };

        $scope.undoSelection = function(){
            MassChangeService.clearSelection();
            $scope.massChange = MassChangeService.getSelected();
            $scope.selectedElements = MassChangeService.getCount();
        };

        $scope.getObjectForDelete = function(host, service){
            var object = {};
            object[service.id] = host.Host.hostname + '/' + service.servicename;
            return object;
        };

        $scope.getObjectsForDelete = function(){
            var objects = {};
            var selectedObjects = MassChangeService.getSelected();
            for(var key in $scope.serverResult){
                for(var id in selectedObjects){
                    if(id == $scope.serverResult[key].Service.id){
                        objects[id] =
                            $scope.serverResult[key].Host.hostname + '/' +
                            $scope.serverResult[key].Service.servicename;
                    }

                }
            }
            return objects;
        };

        $scope.linkForCopy = function(){
            var ids = Object.keys(MassChangeService.getSelected());
            return ids.join(',');
        };


        $scope.changepage = function(page){
            $scope.undoSelection();
            if(page !== $scope.currentPage){
                $scope.currentPage = page;
                $scope.load();
            }
        };


        //Fire on page load
        defaultFilter();
        SortService.setCallback($scope.load);

        $scope.$watch('filter', function(){
            $scope.currentPage = 1;
            $scope.undoSelection();
            $scope.load();
        }, true);


        $scope.$watch('massChange', function(){
            MassChangeService.setSelected($scope.massChange);
            $scope.selectedElements = MassChangeService.getCount();
        }, true);

    });
