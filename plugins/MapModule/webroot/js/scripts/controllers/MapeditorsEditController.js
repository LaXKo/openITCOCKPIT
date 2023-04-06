angular.module('openITCOCKPIT')
    .controller('MapeditorsEditController', function($scope, $http, $stateParams){

        $scope.init = true;
        $scope.id = $stateParams.id;
        $scope.backgrounds = [];
        $scope.lastBackgroundImageToDeletePreventForSave = null;

        $scope.addNewObject = false;
        $scope.action = null;

        $scope.currentItem = {};
        $scope.maxZIndex = 0;
        $scope.clickCount = 1;

        $scope.brokenImageDetected = false;

        $scope.Mapeditor = {
            grid: {
                enabled: true,
                size: 15
            },
            helplines: {
                enabled: true,
                size: 15
            },
            synchronizeGridAndHelplinesSize: true,
            background: {
                position_x: 0,
                position_y: 0,
                width: null,
                height: null
            }
        };

        $scope.helplines = {
            enabled: true,
            size: 15
        };

        $scope.addLink = false;

        $scope.uploadIconSet = false;

        $scope.defaultLayer = '0';

        $scope.visableLayers = {};

        $scope.load = function(){
            $http.get("/map_module/mapeditors/edit/" + $scope.id + ".json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                $scope.map = result.data.map;
                $scope.Mapeditor = result.data.config.Mapeditor;
                $scope.maxUploadLimit = result.data.maxUploadLimit;
                $scope.maxZIndex = result.data.max_z_index;
                $scope.layers = result.data.layers;

                for(var k in $scope.layers){
                    $scope.visableLayers['layer_' + k] = true;
                }

                $scope.currentBackground = $scope.map.Map.background;

                if($scope.init){
                    createDropzones();
                    loadBackgroundImages();

                    setTimeout(makeDraggable, 250);
                }

                $scope.init = false;
            });
        };

        $scope.openChangeMapBackgroundModal = function(){
            if($scope.map.Map.background !== null && $scope.map.Map.background.length > 0){
                if($scope.backgrounds.length === 0){
                    $scope.brokenImageDetected = true;
                }else{
                    $scope.brokenImageDetected = _.filter(
                        $scope.backgrounds, (background) => background.image === $scope.map.Map.background).length === 0;
                }
            }

            $('#changeBackgroundModal').modal('show');
        };

        $scope.changeBackground = function(background){
            if(background !== undefined && $scope.lastBackgroundImageToDeletePreventForSave === background.image){
                $scope.lastBackgroundImageToDeletePreventForSave = null;
                $scope.map.Map.background = null;
                return;
            }
            $http.post("/map_module/mapeditors/saveBackground.json?angular=true",
                {
                    'Map': {
                        id: $scope.id,
                        background: background.image
                    }
                }
            ).then(function(result){
                $scope.errors = {};
                $scope.map.Map.background = background.image;
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });

        };

        var loadBackgroundImages = function(selectedImage){
            $http.get("/map_module/mapeditors/backgroundImages.json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                $scope.backgrounds = result.data.backgrounds;

                if(typeof selectedImage !== "undefined"){
                    $scope.changeBackground({
                        image: selectedImage
                    });
                }
            });
        };

        var createDropzones = function(){
            $('.background-dropzone').dropzone({
                method: 'post',
                maxFilesize: $scope.maxUploadLimit.value, //MB
                acceptedFiles: 'image/*', //mimetypes
                paramName: "file",
                success: function(obj){
                    var $previewElement = $(obj.previewElement);

                    var response = JSON.parse(obj.xhr.response);
                    if(response.response.success){
                        $previewElement.removeClass('dz-processing');
                        $previewElement.addClass('dz-success');

                        new Noty({
                            theme: 'metroui',
                            type: 'success',
                            text: response.response.message,
                            timeout: 3500
                        }).show();

                        loadBackgroundImages(response.response.filename);
                        return;
                    }

                    $previewElement.removeClass('dz-processing');
                    $previewElement.addClass('dz-error');
                    new Noty({
                        theme: 'metroui',
                        type: 'error',
                        text: response.response.message,
                        timeout: 3500
                    }).show();
                },
                error: function(obj, errorMessage, xhr){
                    var $previewElement = $(obj.previewElement);
                    $previewElement.removeClass('dz-processing');
                    $previewElement.addClass('dz-error');

                    if(typeof xhr === "undefined"){
                        new Noty({
                            theme: 'metroui',
                            type: 'error',
                            text: errorMessage,
                            timeout: 3500
                        }).show();
                    }else{
                        var response = JSON.parse(obj.xhr.response);
                        new Noty({
                            theme: 'metroui',
                            type: 'error',
                            text: response.response.message,
                            timeout: 3500
                        }).show();
                    }
                }
            });

            $('.icon-dropzone').dropzone({
                method: 'post',
                maxFilesize: $scope.maxUploadLimit.value, //MB
                acceptedFiles: 'image/*', //mimetypes
                paramName: "file",
                success: function(obj){
                    var $previewElement = $(obj.previewElement);

                    var response = JSON.parse(obj.xhr.response);
                    if(response.response.success){
                        $previewElement.removeClass('dz-processing');
                        $previewElement.addClass('dz-success');

                        new Noty({
                            theme: 'metroui',
                            type: 'success',
                            text: response.response.message,
                            timeout: 3500
                        }).show();

                        loadIcons(response.response.filename);
                        return;
                    }

                    $previewElement.removeClass('dz-processing');
                    $previewElement.addClass('dz-error');
                    new Noty({
                        theme: 'metroui',
                        type: 'error',
                        text: response.response.message,
                        timeout: 3500
                    }).show();
                },
                error: function(obj, errorMessage, xhr){
                    var $previewElement = $(obj.previewElement);
                    $previewElement.removeClass('dz-processing');
                    $previewElement.addClass('dz-error');

                    if(typeof xhr === "undefined"){
                        new Noty({
                            theme: 'metroui',
                            type: 'error',
                            text: errorMessage,
                            timeout: 3500
                        }).show();
                    }else{
                        var response = JSON.parse(obj.xhr.response);
                        new Noty({
                            theme: 'metroui',
                            type: 'error',
                            text: response.response.message,
                            timeout: 3500
                        }).show();
                    }
                }
            });

            $('.iconset-dropzone').dropzone({
                method: 'post',
                maxFilesize: $scope.maxUploadLimit.value, //MB
                acceptedFiles: '.zip', //mimetypes
                paramName: "file",
                success: function(obj){
                    var $previewElement = $(obj.previewElement);

                    var response = JSON.parse(obj.xhr.response);
                    if(response.response.success){
                        $previewElement.removeClass('dz-processing');
                        $previewElement.addClass('dz-success');

                        new Noty({
                            theme: 'metroui',
                            type: 'success',
                            text: response.response.message,
                            timeout: 3500
                        }).show();

                        loadIconsets(response.response.iconsetname);
                        $scope.uploadIconSet = false;
                        return;
                    }

                    $previewElement.removeClass('dz-processing');
                    $previewElement.addClass('dz-error');
                    new Noty({
                        theme: 'metroui',
                        type: 'error',
                        text: response.response.message,
                        timeout: 3500
                    }).show();
                },
                error: function(obj, errorMessage, xhr){
                    var $previewElement = $(obj.previewElement);
                    $previewElement.removeClass('dz-processing');
                    $previewElement.addClass('dz-error');

                    if(typeof xhr === "undefined"){
                        new Noty({
                            theme: 'metroui',
                            type: 'error',
                            text: errorMessage,
                            timeout: 3500
                        }).show();
                    }else{
                        var response = JSON.parse(obj.xhr.response);
                        new Noty({
                            theme: 'metroui',
                            type: 'error',
                            text: response.response.message,
                            timeout: 3500
                        }).show();
                    }
                }
            });
        };

        $scope.deleteBackground = function(background){
            $scope.lastBackgroundImageToDeletePreventForSave = background.image;
            $http.post("/map_module/BackgroundUploads/delete.json?angular=true",
                {
                    'filename': background.image
                }
            ).then(function(result){
                loadBackgroundImages();
                new Noty({
                    theme: 'metroui',
                    type: 'success',
                    text: result.data.response.message,
                    timeout: 3500
                }).show();
            }, function errorCallback(result){
                var text = '';
                if(result.data.hasOwnProperty('message')){
                    text = result.data.message;
                }

                if(result.data.hasOwnProperty('response')){
                    text = result.data.response.message;
                }

                new Noty({
                    theme: 'metroui',
                    type: 'error',
                    text: text,
                    timeout: 3500
                }).show();
            });
        };

        $scope.resetBackground = function(){
            $http.post("/map_module/mapeditors/resetBackground.json?angular=true",
                {
                    'Map': {
                        id: $scope.id
                    }
                }
            ).then(function(){
                $scope.errors = {};
                $scope.map.Map.background = null;
                $scope.brokenImageDetected = false;
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });
        };

        $scope.setCurrentIconset = function(iconset){
            $scope.currentItem.iconset = iconset;
        };

        $scope.addNewLayer = function(){
            $scope.maxZIndex++;

            var newZIndex = $scope.maxZIndex;
            newZIndex = newZIndex.toString();

            $scope.layers[newZIndex] = 'Layer ' + newZIndex;
            $scope.visableLayers['layer_' + newZIndex] = true;

            if($scope.currentItem.hasOwnProperty('z_index')){
                $scope.currentItem.z_index = newZIndex;
            }
        };

        var loadIconsets = function(selectedIconset){
            $http.get("/map_module/mapeditors/getIconsets.json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                $scope.iconsets = [];
                $scope.iconsets = result.data.iconsets;

                if(typeof selectedIconset !== "undefined"){
                    $scope.currentItem.iconset = selectedIconset;
                }
            });
        };

        var loadIcons = function(selectedIcon){
            $http.get("/map_module/mapeditors/getIcons.json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                $scope.icons = result.data.icons;

                if(typeof selectedIcon !== "undefined"){
                    $scope.currentItem.icon = selectedIcon;
                }
            });
        };

        //Gets called by on-click on the map editor contain
        $scope.addNewObjectFunc = function($event){
            if(!$scope.addNewObject){
                return;
            }

            var $mapEditor = $('#map-editor');

            switch($scope.action){
                case 'item':
                    $mapEditor.css('cursor', 'default');
                    $scope.addNewObject = false;

                    $('#addEditMapItemModal').modal('show');

                    // Create currentItem skeleton
                    // Set X and Y poss of the new object
                    $scope.currentItem = {
                        iconset: 'std_mid_64px',
                        z_index: $scope.defaultLayer, //Yes we need this as a string!
                        x: $event.offsetX,
                        y: $event.offsetY,
                        show_label: false,
                        label_possition: 2
                        //x: $event.pageX,
                        //y: $event.pageY
                    };

                    $scope.action = null;
                    break;

                case 'line':
                    if($scope.clickCount === 2){
                        //Endpoint of the line
                        $mapEditor.css('cursor', 'default');
                        $scope.addNewObject = false;
                        $scope.action = null;


                        $scope.currentItem['endX'] = $event.offsetX;
                        $scope.currentItem['endY'] = $event.offsetY;

                        $('#addEditMapLineModal').modal('show');
                    }

                    if($scope.clickCount === 1){

                        $scope.currentItem = {
                            z_index: $scope.defaultLayer, //Yes we need this as a string!
                            startX: $event.offsetX,
                            startY: $event.offsetY,
                            show_label: false
                        };

                        new Noty({
                            theme: 'metroui',
                            type: 'info',
                            layout: 'topCenter',
                            text: 'Click a second time to define the endpoint of the line.',
                            timeout: 3500
                        }).show();
                    }

                    $scope.clickCount++;
                    break;

                case 'gadget':
                    $mapEditor.css('cursor', 'default');
                    $scope.addNewObject = false;

                    $('#addEditMapGadgetModal').modal('show');

                    // Create currentItem skeleton
                    // Set X and Y poss of the new object
                    $scope.currentItem = {
                        type: 'service', //Gadgets are only available for services
                        z_index: $scope.defaultLayer, //Yes we need this as a string!
                        x: $event.offsetX,
                        y: $event.offsetY,
                        show_label: false,
                        label_possition: 2,
                        gadget: 'RRDGraph',
                        size_x: null,
                        size_y: null,
                        metric: null,
                        output_type: null,
                        font_size: 13
                    };

                    $scope.action = null;
                    break;

                case 'text':
                    $mapEditor.css('cursor', 'default');
                    $scope.addNewObject = false;

                    $('#AddEditStatelessTextModal').modal('show');

                    // Create currentItem skeleton
                    // Set X and Y poss of the new object
                    $scope.currentItem = {
                        z_index: $scope.defaultLayer, //Yes we need this as a string!
                        x: $event.offsetX,
                        y: $event.offsetY,
                        text: ''
                    };

                    $scope.action = null;
                    break;

                case 'icon':
                    $mapEditor.css('cursor', 'default');
                    $scope.addNewObject = false;

                    $('#AddEditStatelessIconModal').modal('show');

                    // Create currentItem skeleton
                    // Set X and Y poss of the new object
                    $scope.currentItem = {
                        z_index: $scope.defaultLayer, //Yes we need this as a string!
                        x: $event.offsetX,
                        y: $event.offsetY

                    };

                    $scope.action = null;
                    break;

                case 'summaryItem':
                    $mapEditor.css('cursor', 'default');
                    $scope.addNewObject = false;

                    $('#addEditSummaryItemModal').modal('show');

                    // Create currentItem skeleton
                    // Set X and Y poss of the new object
                    $scope.currentItem = {
                        z_index: $scope.defaultLayer, //Yes we need this as a string!
                        x: $event.offsetX,
                        y: $event.offsetY,
                        show_label: false,
                        label_possition: 2
                    };

                    $scope.action = null;
                    break;

                default:
                    new Noty({
                        theme: 'metroui',
                        type: 'warning',
                        text: 'Unknown action - sorry :(',
                        timeout: 3500
                    }).show();
                    $scope.action = null;
                    $scope.addNewObject = false;
                    $mapEditor.css('cursor', 'default');
                    break;
            }
        };

        $scope.addItem = function(){
            new Noty({
                theme: 'metroui',
                type: 'info',
                layout: 'topCenter',
                text: 'Click at the position on the map, where you want to create a new object.',
                timeout: 3500
            }).show();
            $('#map-editor').css('cursor', 'crosshair');
            $scope.addNewObject = true;
            $scope.action = 'item';
        };

        $scope.editItem = function(item){
            $scope.action = 'item';
            $scope.currentItem = item;
            $('#addEditMapItemModal').modal('show');
        };

        $scope.saveItem = function(action){
            if(typeof action === 'undefined'){
                action = 'add_or_edit';
            }

            $scope.currentItem.map_id = $scope.id;
            $http.post("/map_module/mapeditors/saveItem.json?angular=true",
                {
                    'Mapitem': $scope.currentItem,
                    'action': action
                }
            ).then(function(result){
                $scope.errors = {};
                //Update possition in current scope json data
                if($scope.currentItem.hasOwnProperty('id')){
                    for(var i in $scope.map.Mapitems){
                        if($scope.map.Mapitems[i].id == $scope.currentItem.id){
                            $scope.map.Mapitems[i].x = $scope.currentItem.x;
                            $scope.map.Mapitems[i].y = $scope.currentItem.y;

                            //We are done here
                            break;
                        }
                    }
                }else{
                    //New created item
                    if(typeof $scope.map.Mapitems === "undefined"){
                        $scope.map.Mapitems = [];
                    }
                    $scope.map.Mapitems.push(result.data.Mapitem.Mapitem);
                    setTimeout(makeDraggable, 250);
                }

                $('#addEditMapItemModal').modal('hide');
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });
        };

        $scope.deleteItem = function(){
            $scope.currentItem.map_id = $scope.id;
            $http.post("/map_module/mapeditors/deleteItem.json?angular=true",
                {
                    'Mapitem': $scope.currentItem,
                    'action': 'delete'
                }
            ).then(function(result){
                if(result.data.success){
                    //Remove item from current scope
                    for(var i in $scope.map.Mapitems){
                        if($scope.map.Mapitems[i].id == $scope.currentItem.id){
                            $scope.map.Mapitems.splice(i, 1);

                            //We are done here
                            break;
                        }
                    }
                    genericSuccess();
                }else{
                    genericError();
                }

                $('#addEditMapItemModal').modal('hide');
                $scope.currentItem = {};
            }, function errorCallback(result){
                genericError();
            });
        };

        $scope.addLine = function(){
            new Noty({
                theme: 'metroui',
                type: 'info',
                layout: 'topCenter',
                text: 'Click at the position on the map, where the line should start.',
                timeout: 3500
            }).show();
            $scope.clickCount = 1;
            $('#map-editor').css('cursor', 'crosshair');
            $scope.addNewObject = true;
            $scope.action = 'line';
        };

        $scope.editLine = function(lineItem){
            $scope.action = 'line';
            $scope.currentItem = lineItem;
            $('#addEditMapLineModal').modal('show');
        };

        $scope.saveLine = function(action){
            if(typeof action === 'undefined'){
                action = 'add_or_edit';
            }

            $scope.currentItem.map_id = $scope.id;

            if($scope.currentItem.type === 'stateless'){
                $scope.currentItem.object_id = null;
            }

            $http.post("/map_module/mapeditors/saveLine.json?angular=true",
                {
                    'Mapline': $scope.currentItem,
                    'action': action
                }
            ).then(function(result){
                $scope.errors = {};
                //Update possition in current scope json data
                if($scope.currentItem.hasOwnProperty('id')){
                    for(var i in $scope.map.Maplines){
                        if($scope.map.Maplines[i].id == $scope.currentItem.id){
                            $scope.map.Maplines[i].startY = $scope.currentItem.startY;
                            $scope.map.Maplines[i].endX = $scope.currentItem.endX;
                            $scope.map.Maplines[i].endY = $scope.currentItem.endY;
                            $scope.map.Maplines[i].startX = $scope.currentItem.startX; //Change this last because of $watch in directive

                            //We are done here
                            break;
                        }
                    }
                }else{
                    //New created item
                    if(typeof $scope.map.Maplines === "undefined"){
                        $scope.map.Maplines = [];
                    }

                    $scope.map.Maplines.push(result.data.Mapline.Mapline);
                    setTimeout(makeDraggable, 250);
                }

                $('#addEditMapLineModal').modal('hide');
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });
        };

        $scope.deleteLine = function(){
            $scope.currentItem.map_id = $scope.id;
            $http.post("/map_module/mapeditors/deleteLine.json?angular=true",
                {
                    'Mapline': $scope.currentItem,
                    'action': 'delete'
                }
            ).then(function(result){
                if(result.data.success){
                    //Remove item from current scope
                    for(var i in $scope.map.Maplines){
                        if($scope.map.Maplines[i].id == $scope.currentItem.id){
                            $scope.map.Maplines.splice(i, 1);

                            //We are done here
                            break;
                        }
                    }
                    genericSuccess();
                }else{
                    genericError();
                }

                $('#addEditMapLineModal').modal('hide');
                $scope.currentItem = {};
            }, function errorCallback(result){
                genericError();
            });
        };

        $scope.addGadget = function(){
            new Noty({
                theme: 'metroui',
                type: 'info',
                layout: 'topCenter',
                text: 'Click at the position on the map, where you want to place the new Gadget.',
                timeout: 3500
            }).show();
            $('#map-editor').css('cursor', 'crosshair');
            $scope.addNewObject = true;
            $scope.action = 'gadget';
        };

        $scope.editGadget = function(gadgetItem){
            $scope.action = 'gadget';
            $scope.currentItem = JSON.parse(JSON.stringify(gadgetItem));    //real clone
            $('#addEditMapGadgetModal').modal('show');
        };

        $scope.saveGadget = function(action){
            if(typeof action === 'undefined'){
                action = 'add_or_edit';
            }

            $scope.currentItem.map_id = $scope.id;

            $http.post("/map_module/mapeditors/saveGadget.json?angular=true",
                {
                    'Mapgadget': $scope.currentItem,
                    'action': action
                }
            ).then(function(result){
                if(action === 'resizestop'){
                    genericSuccess();
                    //Nothing needs to be updated
                    return;
                }

                $scope.errors = {};
                //Update possition in current scope json data
                if($scope.currentItem.hasOwnProperty('id')){
                    for(var i in $scope.map.Mapgadgets){
                        if($scope.map.Mapgadgets[i].id == $scope.currentItem.id){

                            if($scope.currentItem.object_id){   // after edit
                                $scope.map.Mapgadgets[i] = $scope.currentItem;
                            }else{                            // only after (draggable) position change
                                $scope.map.Mapgadgets[i].x = $scope.currentItem.x;
                                $scope.map.Mapgadgets[i].y = $scope.currentItem.y;
                            }

                            //We are done here
                            break;
                        }
                    }
                }else{
                    //New created item
                    if(typeof $scope.map.Mapgadgets === "undefined"){
                        $scope.map.Mapgadgets = [];
                    }

                    $scope.map.Mapgadgets.push(result.data.Mapgadget.Mapgadget);
                    setTimeout(makeDraggable, 250);
                }

                $('#addEditMapGadgetModal').modal('hide');
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });
        };

        $scope.deleteGadget = function(){
            $scope.currentItem.map_id = $scope.id;
            $http.post("/map_module/mapeditors/deleteGadget.json?angular=true",
                {
                    'Mapgadget': $scope.currentItem,
                    'action': 'delete'
                }
            ).then(function(result){
                //Remove item from current scope
                if(result.data.success){
                    for(var i in $scope.map.Mapgadgets){
                        if($scope.map.Mapgadgets[i].id == $scope.currentItem.id){
                            $scope.map.Mapgadgets.splice(i, 1);

                            //We are done here
                            break;
                        }
                    }
                    genericSuccess();
                }else{
                    genericError();
                }

                $('#addEditMapGadgetModal').modal('hide');
                $scope.currentItem = {};
            }, function errorCallback(result){
                genericError();
            });
        };

        $scope.addText = function(){
            new Noty({
                theme: 'metroui',
                type: 'info',
                layout: 'topCenter',
                text: 'Click at the position on the map, where you want to create new text',
                timeout: 3500
            }).show();
            $('#map-editor').css('cursor', 'crosshair');
            $scope.addNewObject = true;
            $scope.action = 'text';
            $scope.addLink = false;
            $('#docuText').val('');
        };

        $scope.editText = function(item){
            $scope.action = 'text';
            $scope.currentItem = item;
            $scope.addLink = false;
            $('#docuText').val(item.text);
            $('#AddEditStatelessTextModal').modal('show');
        };

        $scope.saveText = function(action){
            if(typeof action === 'undefined'){
                action = 'add_or_edit';
            }

            $scope.currentItem.map_id = $scope.id;
            if(action === 'add_or_edit'){
                $scope.currentItem.text = $('#docuText').val();
            }

            $http.post("/map_module/mapeditors/saveText.json?angular=true",
                {
                    'Maptext': $scope.currentItem,
                    'action': action
                }
            ).then(function(result){
                $scope.errors = {};
                //Update possition in current scope json data
                if($scope.currentItem.hasOwnProperty('id')){
                    for(var i in $scope.map.Maptexts){
                        if($scope.map.Maptexts[i].id == $scope.currentItem.id){
                            $scope.map.Maptexts[i].x = $scope.currentItem.x;
                            $scope.map.Maptexts[i].y = $scope.currentItem.y;
                            if(action === 'add_or_edit'){
                                $scope.map.Maptexts[i].text = $scope.currentItem.text;
                            }

                            //We are done here
                            break;
                        }
                    }
                }else{
                    //New created item
                    if(typeof $scope.map.Maptexts === "undefined"){
                        $scope.map.Maptexts = [];
                    }

                    $scope.map.Maptexts.push(result.data.Maptext.Maptext);
                    setTimeout(makeDraggable, 250);
                }

                $('#AddEditStatelessTextModal').modal('hide');
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });
        };

        $scope.deleteText = function(){
            $scope.currentItem.map_id = $scope.id;
            $http.post("/map_module/mapeditors/deleteText.json?angular=true",
                {
                    'Maptext': $scope.currentItem,
                    'action': 'delete'
                }
            ).then(function(result){
                //Remove item from current scope
                if(result.data.success){
                    for(var i in $scope.map.Maptexts){
                        if($scope.map.Maptexts[i].id == $scope.currentItem.id){
                            $scope.map.Maptexts.splice(i, 1);
                            $('#docuText').val('');

                            //We are done here
                            break;
                        }
                    }
                    genericSuccess();
                }else{
                    genericError();
                }

                $('#AddEditStatelessTextModal').modal('hide');
                $scope.currentItem = {};
            }, function errorCallback(result){
                genericError();
            });
        };

        $scope.addIcon = function(){
            new Noty({
                theme: 'metroui',
                type: 'info',
                layout: 'topCenter',
                text: 'Click at the position on the map, where you want to create a new icon',
                timeout: 3500
            }).show();
            $('#map-editor').css('cursor', 'crosshair');
            $scope.addNewObject = true;
            $scope.action = 'icon';
        };

        $scope.editIcon = function(item){
            $scope.action = 'icon';
            $scope.currentItem = item;
            $('#AddEditStatelessIconModal').modal('show');
        };

        $scope.saveIcon = function(action){
            if(typeof action === 'undefined'){
                action = 'add_or_edit';
            }

            $scope.currentItem.map_id = $scope.id;

            $http.post("/map_module/mapeditors/saveIcon.json?angular=true",
                {
                    'Mapicon': $scope.currentItem,
                    'action': action
                }
            ).then(function(result){
                $scope.errors = {};
                //Update possition in current scope json data
                if($scope.currentItem.hasOwnProperty('id')){
                    for(var i in $scope.map.Mapicons){
                        if($scope.map.Mapicons[i].id == $scope.currentItem.id){
                            $scope.map.Mapicons[i].x = $scope.currentItem.x;
                            $scope.map.Mapicons[i].y = $scope.currentItem.y;

                            //We are done here
                            break;
                        }
                    }
                }else{
                    //New created item
                    if(typeof $scope.map.Mapicons === "undefined"){
                        $scope.map.Mapicons = [];
                    }

                    $scope.map.Mapicons.push(result.data.Mapicon.Mapicon);
                    setTimeout(makeDraggable, 250);
                }

                $('#AddEditStatelessIconModal').modal('hide');
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });
        };

        $scope.deleteIcon = function(){
            $scope.currentItem.map_id = $scope.id;
            $http.post("/map_module/mapeditors/deleteIcon.json?angular=true",
                {
                    'Mapicon': $scope.currentItem,
                    'action': 'delete'
                }
            ).then(function(result){
                //Remove item from current scope
                for(var i in $scope.map.Mapicons){
                    if($scope.map.Mapicons[i].id == $scope.currentItem.id){
                        $scope.map.Mapicons.splice(i, 1);

                        //We are done here
                        break;
                    }
                }

                $('#AddEditStatelessIconModal').modal('hide');
                genericSuccess();
                $scope.currentItem = {};
            }, function errorCallback(result){
                genericError();
            });
        };

        $scope.deleteIconImage = function(filename){
            $http.post("/map_module/BackgroundUploads/deleteIcon.json?angular=true",
                {
                    'filename': filename
                }
            ).then(function(result){
                loadIcons();
                new Noty({
                    theme: 'metroui',
                    type: 'success',
                    text: result.data.response.message,
                    timeout: 3500
                }).show();
            }, function errorCallback(result){
                var text = '';
                if(result.data.hasOwnProperty('message')){
                    text = result.data.message;
                }

                if(result.data.hasOwnProperty('response')){
                    text = result.data.response.message;
                }

                new Noty({
                    theme: 'metroui',
                    type: 'error',
                    text: text,
                    timeout: 3500
                }).show();
            });
        };

        $scope.addSummaryItem = function(){
            new Noty({
                theme: 'metroui',
                type: 'info',
                layout: 'topCenter',
                text: 'Click at the position on the map, where you want to create a new status summary icon',
                timeout: 3500
            }).show();
            $('#map-editor').css('cursor', 'crosshair');
            $scope.addNewObject = true;
            $scope.action = 'summaryItem';
        };

        $scope.editSummaryItem = function(item){
            $scope.action = 'summaryItem';
            $scope.currentItem = item;
            $('#addEditSummaryItemModal').modal('show');
        };

        $scope.saveSummaryItem = function(action){
            if(typeof action === 'undefined'){
                action = 'add_or_edit';
            }

            $scope.currentItem.map_id = $scope.id;

            $http.post("/map_module/mapeditors/saveSummaryitem.json?angular=true",
                {
                    'Mapsummaryitem': $scope.currentItem,
                    'action': action
                }
            ).then(function(result){
                if(action === 'resizestop'){
                    genericSuccess();
                    //Nothing needs to be updated
                    return;
                }

                $scope.errors = {};
                //Update possition in current scope json data
                if($scope.currentItem.hasOwnProperty('id')){
                    for(var i in $scope.map.Mapsummaryitems){
                        if($scope.map.Mapsummaryitems[i].id == $scope.currentItem.id){
                            $scope.map.Mapsummaryitems[i].x = $scope.currentItem.x;
                            $scope.map.Mapsummaryitems[i].y = $scope.currentItem.y;

                            //We are done here
                            break;
                        }
                    }
                }else{
                    //New created item
                    if(typeof $scope.map.Mapsummaryitems === "undefined"){
                        $scope.map.Mapsummaryitems = [];
                    }

                    $scope.map.Mapsummaryitems.push(result.data.Mapsummaryitem.Mapsummaryitem);
                    setTimeout(makeDraggable, 250);
                }

                $('#addEditSummaryItemModal').modal('hide');
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });
        };

        $scope.deleteSummaryItem = function(){
            $scope.currentItem.map_id = $scope.id;
            $http.post("/map_module/mapeditors/deleteSummaryitem.json?angular=true",
                {
                    'Mapsummaryitem': $scope.currentItem,
                    'action': 'delete'
                }
            ).then(function(result){
                //Remove item from current scope
                if(result.data.success){
                    for(var i in $scope.map.Mapsummaryitems){
                        if($scope.map.Mapsummaryitems[i].id == $scope.currentItem.id){
                            $scope.map.Mapsummaryitems.splice(i, 1);

                            //We are done here
                            break;
                        }
                    }
                    genericSuccess();
                }else{
                    genericError();
                }

                $('#addEditSummaryItemModal').modal('hide');
                $scope.currentItem = {};
            }, function errorCallback(result){
                genericError();
            });
        };

        var loadMetrics = function(){
            $http.get("/map_module/mapeditors/getPerformanceDataMetrics/" + $scope.currentItem.object_id + ".json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                var metrics = {};

                var firstMetric = null;

                for(var metricKey in result.data.perfdata){
                    if(firstMetric === null){
                        firstMetric = metricKey;
                    }
                    var metricDisplayName = result.data.perfdata[metricKey].metric

                    metrics[metricKey] = metricDisplayName;
                }

                if($scope.currentItem.metric === null){
                    $scope.currentItem.metric = firstMetric;
                }

                $scope.metrics = metrics;
            });
        };


        var genericSuccess = function(){
            new Noty({
                theme: 'metroui',
                type: 'success',
                text: 'Data saved successfully',
                timeout: 3500
            }).show();
        };

        var genericError = function(){
            new Noty({
                theme: 'metroui',
                type: 'error',
                text: 'Error while saving data',
                timeout: 3500
            }).show();
        };

        /**
         * Load more objects if the user type to search in a select box.
         * @param searchString
         */
        $scope.loadMoreItemObjects = function(searchString){
            if(typeof $scope.currentItem.type !== "undefined"){

                //Avoid duplicate search requests because of $scope.currentItem.object_id will be set to
                //null if the search result will not contain the current selected object_id. If object_id is null
                //the watchGroup will be triggerd. This will cause duplicate search requests and overwrite results
                var objectId = undefined;
                if(typeof $scope.currentItem.object_id !== 'undefined'){
                    if($scope.currentItem.object_id !== null && $scope.currentItem.object_id > 0){
                        objectId = $scope.currentItem.object_id;
                    }
                }

                if($scope.currentItem.type === 'host'){
                    loadHosts(searchString, objectId);
                }

                if($scope.currentItem.type === 'service'){
                    loadServices(searchString, objectId);
                }

                if($scope.currentItem.type === 'hostgroup'){
                    loadHostgroups(searchString, objectId);
                }

                if($scope.currentItem.type === 'servicegroup'){
                    loadServicegroups(searchString, objectId);
                }

                if($scope.currentItem.type === 'map'){
                    loadMaps(searchString, objectId);
                }
            }
        };

        var loadHosts = function(searchString, selected){
            if(typeof selected === "undefined"){
                selected = [];
            }

            $http.get("/hosts/loadHostsByString.json", {
                params: {
                    'angular': true,
                    'filter[Hosts.name]': searchString,
                    'selected[]': selected,
                    'includeDisabled': 'true'
                }
            }).then(function(result){
                $scope.itemObjects = result.data.hosts;
            });
        };

        var loadServices = function(searchString, selected){
            if(typeof selected === "undefined"){
                selected = [];
            }

            $http.get("/services/loadServicesByString.json", {
                params: {
                    'angular': true,
                    //'filter[Hosts.name]': searchString,
                    'filter[servicename]': searchString,
                    'selected[]': selected,
                    'includeDisabled': 'true'
                }
            }).then(function(result){

                var tmpServices = [];
                for(var i in result.data.services){
                    var tmpService = result.data.services[i];

                    var serviceName = tmpService.value.Service.name;
                    if(serviceName === null || serviceName === ''){
                        serviceName = tmpService.value.Servicetemplate.name;
                    }

                    tmpServices.push({
                        key: tmpService.key,
                        value: tmpService.value.Host.name + '/' + serviceName
                    });

                }

                $scope.itemObjects = tmpServices;
            });
        };


        var loadHostgroups = function(searchString, selected){
            if(typeof selected === "undefined"){
                selected = [];
            }

            $http.get("/hostgroups/loadHostgroupsByString.json", {
                params: {
                    'angular': true,
                    'filter[Containers.name]': searchString,
                    'selected[]': selected
                }
            }).then(function(result){
                $scope.itemObjects = result.data.hostgroups;
            });
        };

        var loadServicegroups = function(searchString, selected){
            if(typeof selected === "undefined"){
                selected = [];
            }

            $http.get("/servicegroups/loadServicegroupsByString.json", {
                params: {
                    'angular': true,
                    'filter[Containers.name]': searchString,
                    'selected[]': selected
                }
            }).then(function(result){
                $scope.itemObjects = result.data.servicegroups;
            });
        };

        var loadMaps = function(searchString, selected){
            if(typeof selected === "undefined"){
                selected = [];
            }

            $http.get("/map_module/mapeditors/loadMapsByString.json", {
                params: {
                    'angular': true,
                    'filter[Maps.name]': searchString,
                    'selected[]': selected,
                    'excluded[]': $scope.id
                }
            }).then(function(result){
                $scope.itemObjects = result.data.maps;
            });
        };

        $scope.changeGridSize = function(size){
            $scope.Mapeditor.grid.size = parseInt(size, 10);
            if($scope.Mapeditor.synchronizeGridAndHelplinesSize){
                if($scope.Mapeditor.grid.size != $scope.Mapeditor.helplines.size){
                    $scope.changeHelplinesSize($scope.Mapeditor.grid.size);
                }
            }
            if($scope.Mapeditor.grid.enabled){
                makeDraggable();
            }
        };

        $scope.changeHelplinesSize = function(size){
            $scope.Mapeditor.helplines.size = parseInt(size, 10);
            if($scope.Mapeditor.synchronizeGridAndHelplinesSize){
                if($scope.Mapeditor.grid.size != $scope.Mapeditor.helplines.size){
                    $scope.changeGridSize($scope.Mapeditor.helplines.size);
                }
            }
        };

        $scope.getHelplinesClass = function(){
            if($scope.Mapeditor.helplines.enabled){
                return 'helplines' + $scope.Mapeditor.helplines.size;
            }
        };

        $scope.setDefaultLayer = function(layerNo){
            $scope.defaultLayer = layerNo.toString();
        };

        $scope.hideLayer = function(key){
            key = key.toString();
            $scope.visableLayers['layer_' + key] = false;

            var objectsToHide = [
                'Mapitems',
                'Maplines',
                'Mapgadgets',
                'Mapicons',
                'Maptexts',
                'Mapsummaryitems'
            ];
            for(var arrayKey in objectsToHide){
                var objectName = objectsToHide[arrayKey];
                if($scope.map.hasOwnProperty(objectName)){
                    for(var i in $scope.map[objectName]){
                        if($scope.map[objectName][i].z_index === key){
                            $scope.map[objectName][i].display = false;
                        }
                    }
                }
            }
        };

        $scope.showLayer = function(key){
            key = key.toString();
            $scope.visableLayers['layer_' + key] = true;

            var objectsToHide = [
                'Mapitems',
                'Maplines',
                'Mapgadgets',
                'Mapicons',
                'Maptexts',
                'Mapsummaryitems'
            ];
            for(var arrayKey in objectsToHide){
                var objectName = objectsToHide[arrayKey];
                if($scope.map.hasOwnProperty(objectName)){
                    for(var i in $scope.map[objectName]){
                        if($scope.map[objectName][i].z_index === key){
                            $scope.map[objectName][i].display = true;
                        }
                    }
                }
            }
        };

        $scope.resetBackgroundSizeSettings = function(){
            $scope.Mapeditor.background.width = null;
            $scope.Mapeditor.background.height = null;
        };

        var makeDraggable = function(){
            var options = {
                grid: false,
                dynamic: false,
                containment: 'parent',
                drag: function(){
                    $(this).css('opacity', '0.5'); // Semi-transparent while being dragged
                    $(this).css('border', '2px dashed #4285F4');
                },
                stop: function(event){
                    if($scope.Mapeditor.grid.enabled){
                        var startPosition = $(this).position();
                        var currentLeftOffset = (Math.round(startPosition.left)) % $scope.Mapeditor.grid.size;
                        var currentTopOffset = (Math.round(startPosition.top)) % $scope.Mapeditor.grid.size;

                        if(currentLeftOffset > 0 || currentTopOffset > 0){
                            $(this).css({
                                'left': Math.round(startPosition.left - currentLeftOffset) + 'px',
                                'top': Math.round(startPosition.top - currentTopOffset) + 'px'
                            });
                        }
                    }

                    var $this = $(this);
                    var x = $this.css('left');
                    var y = $this.css('top');
                    var id = $this.data('id');
                    var type = $this.data('type');
                    x = parseInt(x.replace('px', ''), 10);
                    y = parseInt(y.replace('px', ''), 10);
                    $(this).css('opacity', '1.0'); // Semi-transparent while being dragged
                    $(this).css('border', 'none');

                    switch(type){
                        case 'item':
                            $scope.currentItem = {
                                id: id,
                                x: x,
                                y: y
                            };
                            $scope.saveItem('dragstop');
                            break;

                        case 'line':
                            var position = $this.position();
                            x = position.left;
                            y = position.top;

                            var oldStartX = parseInt($this.data('oldstartx'), 10);
                            var oldStartY = parseInt($this.data('oldstarty'), 10);
                            var oldEndX = parseInt($this.data('oldendx'), 10);
                            var oldEndY = parseInt($this.data('oldendy'), 10);

                            //Get movement distance

                            var distanceX = oldStartX - x;
                            distanceX = distanceX * -1;
                            var distanceY = oldStartY - y;
                            distanceY = distanceY * -1;

                            var endX = oldEndX + distanceX;
                            var endY = oldEndY + distanceY;

                            $scope.currentItem = {
                                id: id,
                                startX: parseInt(x, 10),
                                startY: parseInt(y, 10),
                                endX: parseInt(endX, 10),
                                endY: parseInt(endY, 10)
                            };

                            $scope.saveLine('dragstop');
                            break;

                        case 'gadget':
                            $scope.currentItem = {
                                id: id,
                                x: x,
                                y: y
                            };
                            $scope.saveGadget('dragstop');
                            break;

                        case 'text':
                            $scope.currentItem = {
                                id: id,
                                x: x,
                                y: y
                            };
                            $scope.saveText('dragstop');
                            break;

                        case 'icon':
                            $scope.currentItem = {
                                id: id,
                                x: x,
                                y: y
                            };
                            $scope.saveIcon('dragstop');
                            break;

                        case 'summaryItem':
                            $scope.currentItem = {
                                id: id,
                                x: x,
                                y: y
                            };
                            $scope.saveSummaryItem('dragstop');
                            break;

                        case 'mapBackground':
                            $scope.Mapeditor.background.position_x = x;
                            $scope.Mapeditor.background.position_y = y;
                            break;

                        default:
                            console.log('Unknown map object type');
                            genericError();
                    }
                }
            };

            if($scope.Mapeditor.grid.enabled){
                options['grid'] = [
                    $scope.Mapeditor.grid.size,
                    $scope.Mapeditor.grid.size
                ];
            }

            $('.draggable').draggable(options);

            $('.resizable').resizable({
                aspectRatio: true,
                helper: 'ui-resizable-helper',
                stop: function(event, ui){
                    var $this = $(this);
                    var id = $this.data('id');
                    var type = $this.data('type');

                    var newWidth = parseInt(ui.size.width);
                    var newHeight = parseInt(ui.size.height);

                    switch(type){
                        case 'gadget':
                            for(var key in $scope.map.Mapgadgets){
                                if($scope.map.Mapgadgets[key].id === id){
                                    $scope.map.Mapgadgets[key].size_y = newHeight;
                                    //Set Y value first because $scope.$watch is listening to X value!
                                    $scope.map.Mapgadgets[key].size_x = newWidth;
                                }
                            }

                            $scope.currentItem = {
                                id: id,
                                size_x: newWidth,
                                size_y: newHeight
                            };
                            $scope.saveGadget('resizestop');
                            break;

                        case 'summaryItem':
                            for(var key in $scope.map.Mapsummaryitems){
                                if($scope.map.Mapsummaryitems[key].id === id){
                                    $scope.map.Mapsummaryitems[key].size_y = newHeight;
                                    //Set Y value first because $scope.$watch is listening to X value!
                                    $scope.map.Mapsummaryitems[key].size_x = newWidth;
                                }
                            }

                            $scope.currentItem = {
                                id: id,
                                size_x: newWidth,
                                size_y: newHeight
                            };
                            $scope.saveSummaryItem('resizestop');
                            break;

                        case 'mapBackground':
                            $scope.Mapeditor.background.width = newWidth;
                            $scope.Mapeditor.background.height = newHeight;
                            break;

                        default:
                            console.log('Unknown map object type');
                            genericError();
                    }
                }
            });

            $('.resizable-no-aspect-ratio').resizable({
                aspectRatio: false,
                helper: 'ui-resizable-helper',
                stop: function(event, ui){
                    var $this = $(this);
                    var id = $this.data('id');
                    var type = $this.data('type');

                    var newWidth = parseInt(ui.size.width);
                    var newHeight = parseInt(ui.size.height);

                    switch(type){
                        case 'gadget':
                            for(var key in $scope.map.Mapgadgets){
                                if($scope.map.Mapgadgets[key].id === id){
                                    $scope.map.Mapgadgets[key].size_y = newHeight;
                                    //Set Y value first because $scope.$watch is listening to X value!
                                    $scope.map.Mapgadgets[key].size_x = newWidth;
                                }
                            }

                            $scope.currentItem = {
                                id: id,
                                size_x: newWidth,
                                size_y: newHeight
                            };
                            $scope.saveGadget('resizestop');
                            break;

                        case 'mapBackground':
                            $scope.Mapeditor.background.width = newWidth;
                            $scope.Mapeditor.background.height = newHeight;
                            break;

                        default:
                            console.log('Unknown map object type');
                            genericError();
                    }
                }
            });
        };

        var saveMapeditorSettings = function(){
            $http.post("/map_module/mapeditors/saveMapeditorSettings.json?angular=true",
                {
                    'Map': {
                        id: $scope.id,
                    },
                    'Mapeditor': $scope.Mapeditor
                }
            ).then(function(result){
                $scope.errors = {};
                genericSuccess();
            }, function errorCallback(result){
                if(result.data.hasOwnProperty('error')){
                    $scope.errors = result.data.error;
                }
                genericError();
            });
        };


        $scope.load();
        loadIconsets();
        loadIcons();

        $('#mapToolbar').draggable({
            handle: "#mapToolsDragger",
            containment: "parent"
        });

        var $layersBox = $('#layersBox');
        $layersBox.draggable({
            handle: "#layersBoxDragger",
            containment: "parent"
        });
        $layersBox.resizable({
            ghost: true,
            minWidth: 250,
            maxWidth: 250,
            minHeight: 150,
            stop: function(event, ui){
                var newHeight = parseInt(ui.size.height);
                newHeight = newHeight - 20;
                $('.layersContainer').css({
                    height: newHeight + 'px'
                });

            }
        });


        //jQuery Bases WYSIWYG Editor
        $("[wysiwyg='true']").click(function(){
            var $textarea = $('#docuText');
            var task = $(this).attr('task');
            switch(task){
                case 'bold':
                    $textarea.surroundSelectedText('[b]', '[/b]');
                    break;

                case 'italic':
                    $textarea.surroundSelectedText('[i]', '[/i]');
                    break;

                case 'underline':
                    $textarea.surroundSelectedText('[u]', '[/u]');
                    break;

                case 'left':
                    $textarea.surroundSelectedText('[left]', '[/left]');
                    break;

                case 'center':
                    $textarea.surroundSelectedText('[center]', '[/center]');
                    break;

                case 'right':
                    $textarea.surroundSelectedText('[right]', '[/right]');
                    break;

                case 'justify':
                    $textarea.surroundSelectedText('[justify]', '[/justify]');
                    break;
            }
        });

        // Bind click event for color selector
        $("[select-color='true']").click(function(){
            var color = $(this).attr('color');
            var $textarea = $('#docuText');
            $textarea.surroundSelectedText("[color='" + color + "']", '[/color]');
        });

        // Bind click event for font size selector
        $("[select-fsize='true']").click(function(){
            var fontSize = $(this).attr('fsize');
            var $textarea = $('#docuText');
            $textarea.surroundSelectedText("[text='" + fontSize + "']", "[/text]");
        });

        $('#perform-insert-link').click(function(){
            var $textarea = $('#docuText');
            var url = $('#modalLinkUrl').val();
            var description = $('#modalLinkDescription').val();
            var selection = $textarea.getSelection();
            var newTab = $('#modalLinkNewTab').is(':checked') ? " tab" : "";
            $textarea.insertText("[url='" + url + "'" + newTab + "]" + description + '[/url]', selection.start, "collapseToEnd");
            $('#modalLinkUrl').val('');
            $('#modalLinkDescription').val('');
            $scope.addLink = false;
        });
        /***** End WYSIWYG *****/


        $scope.$watchGroup(['currentItem.type', 'currentItem.object_id'], function(){
            //Initial load objects (like hosts or services) if the user pick an object type
            //while creating a new object on the map
            var objectId = undefined;
            if(typeof $scope.currentItem.object_id !== 'undefined'){
                if($scope.currentItem.object_id !== null && $scope.currentItem.object_id > 0){
                    objectId = $scope.currentItem.object_id;
                }
            }

            if(typeof $scope.currentItem.type !== "undefined"){
                if($scope.currentItem.type === 'host'){
                    loadHosts('', objectId);
                }

                if($scope.currentItem.type === 'service'){
                    loadServices('', objectId);
                }

                if($scope.currentItem.type === 'hostgroup'){
                    loadHostgroups('', objectId);
                }

                if($scope.currentItem.type === 'servicegroup'){
                    loadServicegroups('', objectId);
                }

                if($scope.currentItem.type === 'map'){
                    loadMaps('', objectId);
                }
            }

            if($scope.currentItem.hasOwnProperty('gadget') && typeof objectId !== "undefined"){
                if($scope.currentItem.gadget !== 'TrafficLight'){
                    loadMetrics();
                }
            }
        }, true);

        $scope.$watch('Mapeditor.grid.enabled', function(){
            if($scope.init){
                return;
            }
            makeDraggable();
        }, true);

        $scope.$watch('Mapeditor.synchronizeGridAndHelplinesSize', function(){
            if($scope.init){
                return;
            }
            if($scope.Mapeditor.synchronizeGridAndHelplinesSize && ($scope.Mapeditor.helplines.size != $scope.Mapeditor.grid.size)){
                $scope.changeGridSize($scope.Mapeditor.helplines.size);
            }
        }, true);

        $scope.$watch('Mapeditor', function(){
            if($scope.init){
                return;
            }
            saveMapeditorSettings();
        }, true);

    });
