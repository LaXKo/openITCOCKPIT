angular.module('openITCOCKPIT')
    .controller('UsersLdapController', function($scope, $http, $state, NotyService, RedirectService){
        $scope.data = {
            selectedSamAccountNameIndex: null,
            createAnother: false
        };

        $scope.init = true;

        var clearForm = function(){
            $scope.selectedUserContainers = [];
            $scope.selectedUserContainerWithPermission = {};
            $scope.userContainerRoleContainerIds = [];
            $scope.data.selectedSamAccountNameIndex = null;

            $scope.post = {
                User: {
                    firstname: '',
                    lastname: '',
                    email: '',
                    phone: '',
                    is_active: 1,
                    showstatsinmenu: 0,
                    paginatorlength: 25,
                    dashboard_tab_rotation: 0,
                    recursive_browser: 0,
                    dateformat: 'H:i:s - d.m.Y',
                    timezone: 'Europe/Berlin',
                    password: '',
                    confirm_password: '',
                    is_oauth: 0,

                    samaccountname: '',
                    ldap_dn: '',

                    usergroup_id: 0,
                    usercontainerroles: {
                        _ids: []
                    },
                    usercontainerroles_ldap: {
                        _ids: []
                    },
                    ContainersUsersMemberships: {},
                    apikeys: []
                }
            };
        };
        clearForm();

        var getContainerName = function(containerId){
            containerId = parseInt(containerId, 10);
            for(var index in $scope.containers){
                if($scope.containers[index].key === containerId){
                    return $scope.containers[index].value;
                }
            }

            return 'ERROR UNKNOWN CONTAINER';
        };

        $scope.intervalText = 'disabled';

        $scope.loadLdapConfig = function(){
            var params = {
                'angular': true
            };

            $http.get("/angular/ldap_configuration.json", {
                params: params
            }).then(function(result){
                $scope.ldapConfig = result.data.ldapConfig;
            });
        };

        $scope.loadLdapUsersByString = function(searchString){
            $scope.data.selectedSamAccountNameIndex = null;
            $http.get("/users/loadLdapUserByString.json", {
                params: {
                    'angular': true,
                    'samaccountname': searchString
                }
            }).then(function(result){
                $scope.ldapUsers = result.data.ldapUsers;
                $scope.init = false;
            });
        };

        $scope.loadLdapUserDetailsBySamAccountName = function(samAccountName){
            $scope.data.selectedSamAccountNameIndex = null;
            $http.get("/users/loadLdapUserDetails.json", {
                params: {
                    'angular': true,
                    'samaccountname': samAccountName
                }
            }).then(function(result){
                $scope.ldapUser = result.data.ldapUser;
                $scope.selectedUserContainerRolesLdapReadOnly = [];
                $scope.post.User.usercontainerroles_ldap._ids = Object.keys($scope.ldapUser.userContainerRoleContainerPermissionsLdap);
                for(var i in $scope.ldapUser.userContainerRoleContainerPermissionsLdap){
                    $scope.selectedUserContainerRolesLdapReadOnly.push(
                        $scope.ldapUser.userContainerRoleContainerPermissionsLdap[i]._joinData.usercontainerrole_id
                    );
                }
            });
        };

        $scope.createApiKey = function(index){
            $http.get("/profile/create_apikey.json?angular=true")
                .then(function(result){
                    $scope.post.User.apikeys[index].apikey = result.data.apikey;
                });
        };

        $scope.addApikey = function(){
            $scope.post.User.apikeys.push({
                apikey: '',
                description: '',
            });

            // Query new API Key from Server
            var index = $scope.post.User.apikeys.length;
            if(index > 0){
                // Array is not empty so current array index is lenght - 1, arrays start at 0
                index = index - 1;
            }
            $scope.createApiKey(index);
        };

        $scope.removeApikey = function(index){
            $scope.post.User.apikeys.splice(index, 1);
        };

        $scope.loadUserContainerRoles = function(searchString){
            $http.get("/users/loadContainerRoles.json", {
                params: {
                    'angular': true,
                    'filter[Usercontainerroles.name]': searchString,
                    'selected[]': $scope.post.User.usercontainerroles._ids
                }
            }).then(function(result){
                $scope.usercontainerroles = result.data.usercontainerroles;
            });
        };


        $scope.loadContainer = function(){
            $http.get("/containers/loadContainersForAngular.json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                $scope.containers = result.data.containers;
            });
        };

        $scope.loadUsergroups = function(){
            $http.get("/users/loadUsergroups.json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                $scope.usergroups = result.data.usergroups;
            });
        };

        $scope.loadDateformats = function(){
            $http.get("/users/loadDateformats.json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                $scope.dateformats = result.data.dateformats;
                $scope.post.User.dateformat = result.data.defaultDateFormat;
            });
        };

        $scope.loadContainerPermissions = function(){
            if($scope.post.User.usercontainerroles._ids.length === 0){
                $scope.userContainerRoleContainerPermissions = {};
                return;
            }

            $http.get("/users/loadContainerPermissions.json", {
                params: {
                    'angular': true,
                    'usercontainerRoleIds[]': $scope.post.User.usercontainerroles._ids
                }
            }).then(function(result){
                $scope.userContainerRoleContainerPermissions = result.data.userContainerRoleContainerPermissions;
                $scope.userContainerRoleContainerIds = Object.keys($scope.userContainerRoleContainerPermissions).map(function(item){
                    return parseInt(item, 10);
                });
                /*.map(function(key){
                    return [Number(key), $scope.userContainerRoleContainerPermissions[key]];
                });*/
            });
        };

        $scope.$watch('post.User.dashboard_tab_rotation', function(){
            var dashboardTabRotationInterval = $scope.post.User.dashboard_tab_rotation;
            if(dashboardTabRotationInterval === 0){
                $scope.intervalText = 'disabled';
            }else{
                var min = parseInt(dashboardTabRotationInterval / 60, 10);
                var sec = parseInt(dashboardTabRotationInterval % 60, 10);
                if(min > 0){
                    $scope.intervalText = min + ' minutes, ' + sec + ' seconds';
                    return;
                }
                $scope.intervalText = sec + ' seconds';
            }
        }, true);


        $scope.submit = function(){
            //Define $scope.post.User.ContainersUsersMemberships
            var ContainersUsersMemberships = {};
            for(var containerId in $scope.selectedUserContainerWithPermission){
                ContainersUsersMemberships[containerId] = $scope.selectedUserContainerWithPermission[containerId].permission_level;
            }
            $scope.post.User.ContainersUsersMemberships = ContainersUsersMemberships;
            var apikeys = [];
            var apikeysTmp = $scope.post.User.apikeys;
            if($scope.post.User.apikeys.length > 0){
                for(var i in $scope.post.User.apikeys){
                    if($scope.post.User.apikeys[i].apikey != ''){
                        apikeys.push($scope.post.User.apikeys[i]);
                    }
                }
                $scope.post.User.apikeys = apikeys;
            }

            $http.post("/users/addFromLdap.json?angular=true",
                $scope.post
            ).then(function(result){
                var url = $state.href('UsersEdit', {id: result.data.user.id});
                NotyService.genericSuccess({
                    message: '<u><a href="' + url + '" class="txt-color-white"> '
                        + $scope.successMessage.objectName
                        + '</a></u> ' + $scope.successMessage.message
                });


                if($scope.data.createAnother === false){
                    RedirectService.redirectWithFallback('UsersIndex');
                }else{
                    clearForm();
                    $scope.errors = {};
                    NotyService.scrollTop();
                }

                console.log('Data saved successfully');
            }, function errorCallback(result){
                NotyService.genericError();
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                    $scope.post.User.apikeys = apikeysTmp;
                }
            });
        };

        $scope.$watch('post.User.usercontainerroles._ids', function(){
            $scope.loadContainerPermissions();
        }, true);

        $scope.$watch('selectedUserContainers', function(){
            if($scope.selectedUserContainers.length === 0){
                //No user containers selected
                $scope.selectedUserContainerWithPermission = {};
                return;
            }

            //Add new selected containers
            for(var index in $scope.selectedUserContainers){
                var containerId = $scope.selectedUserContainers[index];
                if(!$scope.selectedUserContainerWithPermission.hasOwnProperty(containerId)){

                    var permission_level = 1;
                    if(containerId === 1){
                        // ROOT_CONTAINER is always read/write !
                        permission_level = 2;
                    }

                    $scope.selectedUserContainerWithPermission[containerId] = {
                        name: getContainerName(containerId),
                        container_id: parseInt(containerId, 10),
                        permission_level: permission_level.toString() //String is required for AngularJS Front End value="2"
                    };
                }
            }

            //Remove "unselected" containers
            for(var containerId in $scope.selectedUserContainerWithPermission){
                //Do not mix strings and integers with indexOf !
                containerId = parseInt(containerId, 10);
                if($scope.selectedUserContainers.indexOf(containerId) === -1){
                    //Container was removed from select box - remove it from permissions object
                    delete $scope.selectedUserContainerWithPermission[containerId];
                }
            }
        }, true);

        $scope.$watch('data.selectedSamAccountNameIndex', function(){
            if($scope.init){
                return;
            }

            var index = parseInt($scope.data.selectedSamAccountNameIndex, 10);
            if(typeof $scope.ldapUsers[index] !== "undefined"){
                $scope.post.User.firstname = $scope.ldapUsers[index].givenname;
                $scope.post.User.lastname = $scope.ldapUsers[index].sn;
                $scope.post.User.email = $scope.ldapUsers[index].email;
                $scope.post.User.samaccountname = $scope.ldapUsers[index].samaccountname;
                $scope.post.User.ldap_dn = $scope.ldapUsers[index].dn;

                // Load LDAP groups of selected user
                $scope.loadLdapUserDetailsBySamAccountName($scope.ldapUsers[index].samaccountname);
            }
        });

        $scope.loadLdapConfig();
        $scope.loadLdapUsersByString('');
        $scope.loadUserContainerRoles();
        $scope.loadContainer();
        $scope.loadUsergroups();
        $scope.loadDateformats();

    });
