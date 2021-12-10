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
        <a ui-sref="ServiceescalationsIndex">
            <i class="fa fa-bomb"></i> <?php echo __('Service escalations'); ?>
        </a>
    </li>
    <li class="breadcrumb-item">
        <i class="fa fa-list"></i> <?php echo __('Overview'); ?>
    </li>
</ol>

<!-- ANGAULAR DIRECTIVES -->
<massdelete></massdelete>

<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    <?php echo __('Service Escalations'); ?>
                    <span class="fw-300"><i><?php echo __('overview'); ?></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-xs btn-default mr-1 shadow-0" ng-click="load()">
                        <i class="fas fa-sync"></i> <?php echo __('Refresh'); ?>
                    </button>
                    <?php if ($this->Acl->hasPermission('add', 'serviceescalations')): ?>
                        <button class="btn btn-xs btn-success mr-1 shadow-0" ui-sref="ServiceescalationsAdd">
                            <i class="fas fa-plus"></i> <?php echo __('New'); ?>
                        </button>
                    <?php endif; ?>

                    <button class="btn btn-xs btn-primary shadow-0" ng-click="triggerFilter()">
                        <i class="fas fa-filter"></i> <?php echo __('Filter'); ?>
                    </button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">

                    <!-- Start Filter -->
                    <div class="list-filter card margin-bottom-10" ng-show="showFilter">
                        <div class="card-header">
                            <i class="fa fa-filter"></i> <?php echo __('Filter'); ?>
                        </div>
                        <div class="card-body">
                            <!-- servicename/servicegroup filter start -->
                            <div class="row">
                                <div class="col-lg-6 bordered-vertical-on-left">
                                    <div class="col-xs-12 col-lg-12 margin-bottom-10">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-cog"></i></span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm"
                                                       placeholder="<?php echo __('Filter by service name'); ?>"
                                                       ng-model="filter.Services.servicename"
                                                       ng-model-options="{debounce: 500}"
                                                       ng-focus="serviceFocus=true;filter.ServicesExcluded.servicename='';serviceExcludeFocus=false;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row padding-top-5 padding-bottom-5 padding-left-25">
                                        <div class="col-xs-12 help-block helptext text-info">
                                            <i class="fa fa-info-circle text-info"></i>
                                            <?php echo __('You can either search for  <b>"service"</b> OR <b>"excluded service"</b>. Opposing Field will be reset automatically'); ?>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-lg-12 margin-bottom-10">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <div class="icon-stack">
                                                            <i class="fas fa-cog opacity-100 "></i>
                                                            <i class="fas fa-exclamation-triangle opacity-100 fa-xs text-danger cornered cornered-lr"></i>
                                                        </div>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control "
                                                       placeholder="<?php echo __('Filter by excluded service name'); ?>"
                                                       ng-model="filter.ServicesExcluded.servicename"
                                                       ng-model-options="{debounce: 500}"
                                                       ng-focus="serviceExcludeFocus=true;filter.Services.servicename='';serviceFocus=false;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 bordered-vertical-on-left">
                                    <div class="col-xs-12 col-lg-12 margin-bottom-10">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-cogs"></i></span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm"
                                                       placeholder="<?php echo __('Filter by service group'); ?>"
                                                       ng-model="filter.Servicegroups.name"
                                                       ng-model-options="{debounce: 500}"
                                                       ng-focus="servicegroupFocus=true;filter.ServicegroupsExcluded.name='';servicegroupExcludeFocus=false;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row padding-top-5 padding-bottom-5 padding-left-25">
                                        <div class="col-xs-12 no-padding help-block helptext text-info">
                                            <i class="fa fa-info-circle text-info"></i>
                                            <?php echo __('You can either search for  <b>"service group"</b> OR <b>"excluded service group"</b>.  Opposing Field will be reset automatically'); ?>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-lg-12 margin-bottom-10">
                                        <div class="form-group">
                                            <div class="input-group  input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <div class="icon-stack">
                                                            <i class="fas fa-cogs opacity-100 "></i>
                                                            <i class="fas fa-exclamation-triangle opacity-100 fa-xs text-danger cornered cornered-lr"></i>
                                                        </div>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control"
                                                       placeholder="<?php echo __('Filter by excluded service group'); ?>"
                                                       ng-model="filter.ServicegroupsExcluded.name"
                                                       ng-model-options="{debounce: 500}"
                                                       ng-focus="servicegroupExcludeFocus=true;filter.Servicegroups.name='';servicegroupFocus=false;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- servicename/servicegroup filter end -->

                            <!-- notification / Escalate on filter start -->
                            <div class="row">
                                <div class="col-xs-12 col-lg-3">
                                    <fieldset>
                                        <h5><?php echo __('Notification options'); ?></h5>
                                        <div class="col-xs-12 col-lg-12 margin-bottom-10">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="fa fa-envelope"></i></span>
                                                    </div>
                                                    <input class="form-control form-control-sm"
                                                           type="number"
                                                           min="1"
                                                           step="1"
                                                           placeholder="<?php echo __('Filter by first notification'); ?>"
                                                           ng-model="filter.Serviceescalations.first_notification"
                                                           ng-model-options="{debounce: 500}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-lg-12 margin-bottom-10">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="fa fa-envelope"></i></span>
                                                    </div>
                                                    <input class="form-control form-control-sm"
                                                           type="number"
                                                           min="0"
                                                           step="1"
                                                           placeholder="<?php echo __('Filter by last notification'); ?>"
                                                           ng-model="filter.Serviceescalations.last_notification"
                                                           ng-model-options="{debounce: 500}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-lg-12 margin-bottom-10">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="fa fa-clock"></i></span>
                                                    </div>
                                                    <input class="form-control form-control-sm"
                                                           type="number"
                                                           min="0"
                                                           step="1"
                                                           placeholder="<?php echo __('Filter by notification interval'); ?>"
                                                           ng-model="filter.Serviceescalations.notification_interval"
                                                           ng-model-options="{debounce: 500}">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-12 col-lg-3">
                                    <fieldset>
                                        <h5><?php echo __('Escalate on ...'); ?></h5>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                       id="statusFilterOk"
                                                       class="custom-control-input"
                                                       name="checkbox"
                                                       checked="checked"
                                                       ng-model="filter.Serviceescalations.escalate_on_recovery"
                                                       ng-model-options="{debounce: 500}"
                                                       ng-true-value="1"
                                                       ng-false-value="">
                                                <label class="custom-control-label custom-control-label-ok"
                                                       for="statusFilterOk"><?php echo __('Ok'); ?></label>
                                            </div>

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                       id="statusFilterWarning"
                                                       class="custom-control-input"
                                                       name="checkbox"
                                                       checked="checked"
                                                       ng-model="filter.Serviceescalations.escalate_on_warning"
                                                       ng-model-options="{debounce: 500}"
                                                       ng-true-value="1"
                                                       ng-false-value="">
                                                <label class="custom-control-label custom-control-label-warning"
                                                       for="statusFilterWarning"><?php echo __('Warning'); ?></label>
                                            </div>

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                       id="statusFilterCritical"
                                                       class="custom-control-input"
                                                       name="checkbox"
                                                       checked="checked"
                                                       ng-model="filter.Serviceescalations.escalate_on_critical"
                                                       ng-model-options="{debounce: 500}"
                                                       ng-true-value="1"
                                                       ng-false-value="">
                                                <label class="custom-control-label custom-control-label-critical"
                                                       for="statusFilterCritical"><?php echo __('Critical'); ?></label>
                                            </div>

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                       id="statusFilterUnknown"
                                                       class="custom-control-input"
                                                       name="checkbox"
                                                       checked="checked"
                                                       ng-model="filter.Serviceescalations.escalate_on_unknown"
                                                       ng-model-options="{debounce: 500}"
                                                       ng-true-value="1"
                                                       ng-false-value="">
                                                <label class="custom-control-label custom-control-label-unknown"
                                                       for="statusFilterUnknown"><?php echo __('Unknown'); ?></label>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <!-- notification / Escalate on filter end -->
                            <div class="float-right">
                                <button type="button" ng-click="resetFilter()"
                                        class="btn btn-xs btn-danger">
                                    <?php echo __('Reset Filter'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter -->

                    <div class="frame-wrap">
                        <table class="table table-striped m-0 table-bordered table-hover table-sm">
                            <thead>
                            <tr>
                                <th class="text-align-center"><i class="fa fa-check-square"
                                                                 aria-hidden="true"></i></th>
                                <th><?php echo __('Services'); ?></th>
                                <th><?php echo __('Excluded services'); ?></th>
                                <th><?php echo __('Service groups'); ?></th>
                                <th><?php echo __('Excluded service groups'); ?></th>
                                <th><?php echo __('First'); ?></th>
                                <th><?php echo __('Last'); ?></th>
                                <th><?php echo __('Interval'); ?></th>
                                <th><?php echo __('Timeperiod'); ?></th>
                                <th><?php echo __('Contacts'); ?></th>
                                <th><?php echo __('Contact groups'); ?></th>
                                <th class="no-sort"><?php echo __('Options'); ?></th>
                                <th class="no-sort text-center width-60"><i class="fa fa-gear"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="serviceescalation in serviceescalations">
                                <td class="text-center" class="width-15">
                                    <?php if ($this->Acl->hasPermission('delete', 'serviceescalations')): ?>
                                        <input type="checkbox"
                                               ng-model="massChange[serviceescalation.id]"
                                               ng-show="serviceescalation.allowEdit">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li ng-repeat="service in serviceescalation.services"
                                            title="{{service.servicename}}">
                                            <div
                                                class="label-group label-breadcrumb label-breadcrumb-success padding-2">
                                                <label class="badge badge-success label-xs">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </label>
                                                <?php if ($this->Acl->hasPermission('edit', 'services')): ?>
                                                    <a ui-sref="ServicesEdit({id: service.id})"
                                                       class="badge badge-light label-xs">
                                                        {{service.servicename}}
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-light label-xs">
                                                        {{service.servicename}}
                                                    </span>
                                                <?php endif; ?>
                                                <i ng-if="service.disabled == 1"
                                                   class="fa fa-power-off text-danger"
                                                   title="disabled" aria-hidden="true"></i>
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li ng-repeat="service in serviceescalation.services_excluded"
                                            title="{{service.servicename}}">
                                            <div class="label-group label-breadcrumb label-breadcrumb-danger padding-2">
                                                <label class="badge badge-danger label-xs">
                                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                                </label>
                                                <?php if ($this->Acl->hasPermission('edit', 'services')): ?>
                                                    <a ui-sref="ServicesEdit({id: service.id})"
                                                       class="badge badge-light label-xs">
                                                        {{service.servicename}}
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-light label-xs">
                                                        {{service.servicename}}
                                                    </span>
                                                <?php endif; ?>
                                                <i ng-if="service.disabled == 1"
                                                   class="fa fa-power-off text-danger"
                                                   title="disabled" aria-hidden="true"></i>
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li ng-repeat="servicegroup in serviceescalation.servicegroups">
                                            <div class="label-group label-breadcrumb label-breadcrumb-success padding-2"
                                                 title="{{servicegroup.container.name}}">
                                                <label class="badge badge-success label-xs">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </label>
                                                <?php if ($this->Acl->hasPermission('edit', 'servicegroups')): ?>
                                                    <a ui-sref="ServicegroupsEdit({id: servicegroup.id})"
                                                       class="badge badge-light label-xs">
                                                        {{servicegroup.container.name}}
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-light label-xs">
                                                        {{servicegroup.container.name}}
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li ng-repeat="servicegroup in serviceescalation.servicegroups_excluded">
                                            <div class="label-group label-breadcrumb label-breadcrumb-danger padding-2"
                                                 title="{{servicegroup.container.name}}">
                                                <label class="badge badge-danger label-xs">
                                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                                </label>
                                                <?php if ($this->Acl->hasPermission('edit', 'servicegroups')): ?>
                                                    <a ui-sref="ServicegroupsEdit({id: servicegroup.id})"
                                                       class="badge badge-light label-xs">
                                                        {{servicegroup.container.name}}
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-light label-xs">
                                                        {{servicegroup.container.name}}
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    {{ serviceescalation.first_notification }}
                                </td>
                                <td>
                                    {{ serviceescalation.last_notification }}
                                </td>
                                <td>
                                    {{ serviceescalation.notification_interval }}
                                </td>
                                <td>
                                    <?php if ($this->Acl->hasPermission('edit', 'timeperiods')): ?>
                                        <a ui-sref="TimeperiodsEdit({id: serviceescalation.timeperiod.id})">{{
                                            serviceescalation.timeperiod.name }}</a>
                                    <?php else: ?>
                                        {{ serviceescalation.timeperiod.name }}
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li ng-repeat="contact in serviceescalation.contacts">
                                            <?php if ($this->Acl->hasPermission('edit', 'contacts')): ?>
                                                <a ui-sref="ContactsEdit({id: contact.id})">
                                                    {{ contact.name }}
                                                </a>
                                            <?php else: ?>
                                                {{ contact.name }}
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li ng-repeat="contactgroup in serviceescalation.contactgroups">
                                            <?php if ($this->Acl->hasPermission('edit', 'contactgroups')): ?>
                                                <a ui-sref="ContactgroupsEdit({id: contactgroup.id})">
                                                    {{ contactgroup.container.name }}
                                                </a>
                                            <?php else: ?>
                                                {{ contactgroup.container.name }}
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </td>
                                <td class="text-align-center">
                                    <div>
                                        <span class="label-forced badge-success margin-right-5"
                                              title="<?php echo __('Recovery'); ?>"
                                              ng-show="serviceescalation.escalate_on_recovery">
                                            <?php echo __('R'); ?>
                                        </span>
                                        <span class="label-forced badge-warning margin-right-5"
                                              title="<?php echo __('Warning'); ?>"
                                              ng-show="serviceescalation.escalate_on_warning">
                                            <?php echo __('W'); ?>
                                        </span>
                                        <span class="label-forced badge-danger margin-right-5"
                                              title="<?php echo __('Critical'); ?>"
                                              ng-show="serviceescalation.escalate_on_critical">
                                            <?php echo __('C'); ?>
                                        </span>
                                        <span class="label-forced badge-secondary margin-right-5"
                                              title="<?php echo __('Unknown'); ?>"
                                              ng-show="serviceescalation.escalate_on_unknown">
                                            <?php echo __('U'); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-xs" role="group">
                                        <?php if ($this->Acl->hasPermission('edit', 'serviceescalations')): ?>
                                            <a ui-sref="ServiceescalationsEdit({id: serviceescalation.id})"
                                               ng-if="serviceescalation.allowEdit"
                                               class="btn btn-default btn-lower-padding">
                                                <i class="fa fa-cog"></i>
                                            </a>
                                            <a href="javascript:void(0);"
                                               ng-if="!serviceescalation.allowEdit"
                                               class="btn btn-default btn-lower-padding">
                                                <i class="fa fa-cog"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="javascript:void(0);"
                                               class="btn btn-default btn-lower-padding disabled">
                                                <i class="fa fa-cog"></i></a>
                                        <?php endif; ?>
                                        <button type="button"
                                                class="btn btn-default dropdown-toggle btn-lower-padding"
                                                data-toggle="dropdown">
                                            <i class="caret"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <?php if ($this->Acl->hasPermission('edit', 'serviceescalations')): ?>
                                                <a ui-sref="ServiceescalationsEdit({id: serviceescalation.id})"
                                                   ng-if="serviceescalation.allowEdit"
                                                   class="dropdown-item">
                                                    <i class="fa fa-cog"></i>
                                                    <?php echo __('Edit'); ?>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($this->Acl->hasPermission('delete', 'serviceescalations')): ?>
                                                <div class="dropdown-divider"></div>
                                                <a ng-click="confirmDelete(getObjectForDelete(serviceescalation))"
                                                   ng-if="serviceescalation.allowEdit"
                                                   href="javascript:void(0);"
                                                   class="dropdown-item txt-color-red">
                                                    <i class="fa fa-trash"></i>
                                                    <?php echo __('Delete'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="margin-top-10" ng-show="serviceescalations.length == 0">
                            <div class="text-center text-danger italic">
                                <?php echo __('No entries match the selection'); ?>
                            </div>
                        </div>
                        <div class="row margin-top-10 margin-bottom-10">
                            <div class="col-xs-12 col-md-2 text-muted text-center">
                                <span ng-show="selectedElements > 0">({{selectedElements}})</span>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <span ng-click="selectAll()" class="pointer">
                                    <i class="fas fa-lg fa-check-square"></i>
                                    <?php echo __('Select all'); ?>
                                </span>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <span ng-click="undoSelection()" class="pointer">
                                    <i class="fas fa-lg fa-square"></i>
                                    <?php echo __('Undo selection'); ?>
                                </span>
                            </div>
                            <?php if ($this->Acl->hasPermission('delete', 'serviceescalations')): ?>
                                <div class="col-xs-12 col-md-2 txt-color-red">
                                    <span ng-click="confirmDelete(getObjectsForDelete())" class="pointer">
                                        <i class="fas fa-trash"></i>
                                        <?php echo __('Delete selected'); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <scroll scroll="scroll" click-action="changepage" ng-if="scroll"></scroll>
                        <paginator paging="paging" click-action="changepage" ng-if="paging"></paginator>
                        <?php echo $this->element('paginator_or_scroll'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
