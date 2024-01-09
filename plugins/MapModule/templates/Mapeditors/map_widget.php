<?php
// Copyright (C) <2015>  <it-novum GmbH>
//
// This file is dual licensed
//
// 1.
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, version 3 of the License.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// 2.
//  If you purchased an openITCOCKPIT Enterprise Edition you can use this file
//  under the terms of the openITCOCKPIT Enterprise Edition license agreement.
//  License agreement and license key will be shipped with the order
//  confirmation.
?>
<div>
    <flippy vertical
            class="col-lg-12"
            flip="['custom:FLIP_EVENT_OUT']"
            flip-back="['custom:FLIP_EVENT_IN']"
            duration="800"
            timing-function="ease-in-out">

        <flippy-front class="fixFlippy">
            <div class="row">
                <div class="col-12">
                    <a href="javascript:void(0);" class="btn btn-default btn-xs txt-color-blueDark"
                       ng-click="showConfig()" ng-hide="readOnly">
                        <i class="fa fa-cog fa-sm"></i>
                    </a>
                    <span ng-show="map.map_id === null" class="text-info padding-left-20">
                        <?php echo __('No element selected'); ?>
                    </span>
                </div>
            </div>
            <div class="row pt-1">
                <div class="col-12 no-padding">
                    <div
                        class="no-padding"
                        mapeditor-view=""
                        map-id="map.map_id"
                        ng-if="map.map_id">
                    </div>
                </div>
            </div>
        </flippy-front>
        <flippy-back class="fixFlippy">
            <a href="javascript:void(0);" class="btn btn-default btn-xs txt-color-blueDark" ng-click="hideConfig()">
                <i class="fa fa-eye fa-sm"></i>
            </a>
            <div class="padding-top-10">
                <div class="form-group">
                    <div class="row">
                        <label class="col-xs-12 col-lg-12 control-label">
                            <?php echo __('Map'); ?>
                        </label>
                        <div class="col-xs-12 col-lg-12">
                            <select data-placeholder="<?php echo __('Please choose'); ?>"
                                    class="form-control"
                                    chosen="availableMaps"
                                    callback="loadMaps"
                                    ng-options="availableMap.key as availableMap.value for availableMap in availableMaps"
                                    ng-model="map.map_id">
                            </select>

                            <div ng-repeat="error in errors.Service">
                                <div class="help-block text-danger">{{ error }}</div>
                            </div>
                        </div>
                    </div>
                    <br/>

                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary pull-right" ng-click="saveMap()">
                                <?php echo __('Save'); ?>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </flippy-back>
    </flippy>
</div>
