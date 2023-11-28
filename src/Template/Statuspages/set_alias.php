<?php
// Copyright (C) <2015>  <it-novum GmbH>
//
// This file is dual licensed
//
// 1.
//	This program is free software: you can redistribute it and/or modify
//	it under the terms of the GNU General Public License as published by
//	the Free Software Foundation, version 3 of the License.
//
//	This program is distributed in the hope that it will be useful,
//	but WITHOUT ANY WARRANTY; without even the implied warranty of
//	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//	GNU General Public License for more details.
//
//	You should have received a copy of the GNU General Public License
//	along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// 2.
//	If you purchased an openITCOCKPIT Enterprise Edition you can use this file
//	under the terms of the openITCOCKPIT Enterprise Edition license agreement.
//	License agreement and license key will be shipped with the order
//	confirmation.
?>
<ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item">
        <a ui-sref="DashboardsIndex">
            <i class="fa fa-home"></i> <?php echo __('Home'); ?>
        </a>
    </li>
    <li class="breadcrumb-item">
        <a ui-sref="StatuspagesIndex">
            <i class="fas fa-info-circle"></i> <?php echo __('Status pages'); ?>
        </a>
    </li>
    <li class="breadcrumb-item">
        <i class="fa fa-plus"></i> <?php echo __('Aliases'); ?>
    </li>
</ol>

<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
            <h2>
                <?php echo __('Status page'); ?>
                <span class="fw-300"><i><?php echo __('Set alias names'); ?> </i></span>
            </h2>
            <div class="panel-toolbar">
                <?php if ($this->Acl->hasPermission('index', 'statuspages')): ?>
                    <a back-button href="javascript:void(0);" fallback-state='StatuspagesIndex'
                       class="btn btn-default btn-xs mr-1 shadow-0">
                        <i class="fas fa-long-arrow-alt-left"></i> <?php echo __('Back'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
            <form ng-submit="submit();" class="form-horizontal"
                  ng-init="successMessage=
                        {objectName : '<?php echo __('Statuspage'); ?>' , message: '<?php echo __('created successfully'); ?>'}">
                <!-- Hosts start -->

                <span ng-if="post.Statuspage.hostgroups.length > 0">
                    <table class="table">
                        <thead>
                        <tr class="d-flex">
                            <th class="col-5"><?= __('Hostgroup name'); ?></th>
                            <th class="col-7"><?= __('Display name'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="hostgroup in post.Statuspage.hostgroups" class="d-flex">
                            <td class="col-5">{{hostgroup.name}}</td>
                            <td class="col-7">
                                <input
                                        class="form-control"
                                        type="text"
                                        ng-model="hostgroup._joinData.display_alias">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </span>
                <hr>

                <span ng-if="post.Statuspage.servicegroups.length > 0">
                    <table class="table">
                        <thead>
                        <tr class="d-flex">
                            <th class="col-5"><?= __('Servicegroup name'); ?></th>
                            <th class="col-7"><?= __('Display name'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="servicegroup in post.Statuspage.servicegroups" class="d-flex">
                            <td class="col-5">{{servicegroup.name}}</td>
                            <td class="col-7">
                                <input
                                    class="form-control"
                                    type="text"
                                    ng-model="servicegroup._joinData.display_alias">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </span>
                <hr>

                <span ng-if="post.Statuspage.hosts.length > 0">
                    <table class="table">
                        <thead>
                        <tr class="d-flex">
                            <th class="col-5"><?= __('Host name'); ?></th>
                            <th class="col-7"><?= __('Display name'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="host in post.Statuspage.hosts" class="d-flex">
                            <td class="col-5">{{host.name}}</td>
                            <td class="col-7">
                                <input
                                    class="form-control"
                                    type="text"
                                    ng-model="host._joinData.display_alias">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </span>
                <hr>
                <span ng-if="post.Statuspage.services.length > 0">
                    <table class="table">
                        <thead>
                        <tr class="d-flex">
                            <th class="col-3"><?= __('Service name'); ?></th>
                            <th class="col-2"><?= __('From host'); ?></th>
                            <th class="col-7"><?= __('Display name'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="service in post.Statuspage.services" class="d-flex">
                            <td class="col-3">{{service.servicename}}</td>
                            <td class="col-2">{{service.hostname}}</td>
                            <td class="col-7">
                                <input
                                    class="form-control"
                                    type="text"
                                    ng-model="service._joinData.display_alias">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </span>
                    <hr>
                <div class="card margin-top-10">
                    <div class="card-body">

                    <div class="float-right">
                        <a back-button href="javascript:void(0);" fallback-state='StatuspagesIndex'
                           class="btn btn-default"><?php echo __('Cancel'); ?>
                        </a>
                        <?php if ($this->Acl->hasPermission('add', 'statuspages')): ?>
                            <button class="btn btn-primary" type="submit">
                                <?php echo __('Save'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

