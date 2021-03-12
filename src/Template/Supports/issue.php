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

/**
 * @var \App\View\AppView $this
 * @var bool $hasLicense
 * @var bool $supportModuleInstalled
 */


?>
<ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item">
        <a ui-sref="DashboardsIndex">
            <i class="fa fa-home"></i> <?php echo __('Home'); ?>
        </a>
    </li>
    <li class="breadcrumb-item">
        <a ui-sref="SupportsIssue">
            <i class="fa fa-bug"></i> <?php echo __('Support'); ?>
        </a>
    </li>
    <li class="breadcrumb-item">
        <i class="fas fa-clipboard-list"></i> <?php echo __('Issue'); ?>
    </li>
</ol>


<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    <?php echo __('Support'); ?>
                    <span class="fw-300"><i><?php echo __('Report an issue'); ?></i></span>
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">

                    <div class="row">
                        <div class="col-4 padding-bottom-10">
                            <div class="card supportCard">
                                <div class="card-header enterprise-bg-header text-white">
                                    <h4 class="pm-h4">
                                        <?= __('Commercial support'); ?>
                                    </h4>
                                    <div class="float-right italic">
                                        <?= h('it-novum GmbH'); ?>
                                    </div>
                                </div>
                                <div class="card-body packagemanagerCardBody">
                                    <div class="text">

                                        <ul class="list-unstyled">
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('Get Professional Support'); ?>
                                            </li>
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('Write tickets directly from your openITCOCKPIT system'); ?>
                                            </li>
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('Overview of your support tickets'); ?>
                                            </li>
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('Access to a large variety of extensions for your openITCOCKPIT'); ?>
                                            </li>
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('Get in Touch with us!'); ?>
                                            </li>
                                        </ul>

                                    </div>

                                </div>
                                <div class="card-footer">
                                    <div class="float-right">
                                        <?php if ($supportModuleInstalled): ?>
                                            <?php if ($hasLicense): ?>
                                                <button class="btn btn-success btn-sm" ui-sref="SupportsIndex">
                                                    <i class="fas fa-headset"></i>
                                                    <?= __('Enterprise Support'); ?>
                                                </button>
                                            <?php else: ?>
                                                <a
                                                        href="https://it-novum.com/en/contact/contact-form-itsm/"
                                                        target="_blank"
                                                        class="btn btn-primary btn-sm float-right">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <?= __('Request a quote'); ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($hasLicense): ?>
                                                <a href="mailto:support@itsm.it-novum.com"
                                                   class="btn btn-primary btn-sm"
                                                   role="button">
                                                    <i class="far fa-envelope"></i>
                                                    <?= __('Create a Ticket'); ?>
                                                </a>
                                            <?php else: ?>
                                                <a
                                                        href="https://it-novum.com/en/contact/contact-form-itsm/"
                                                        target="_blank"
                                                        class="btn btn-primary btn-sm float-right">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <?= __('Request a quote'); ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="col-4 padding-bottom-10">
                            <div class="card supportCard">
                                <div class="card-header community-bg-header text-white">
                                    <h4 class="pm-h4">
                                        <?= __('Social Networks'); ?>
                                    </h4>
                                    <div class="float-right italic">
                                        <?= h('Community'); ?>
                                    </div>
                                </div>
                                <div class="card-body packagemanagerCardBody">
                                    <div class="text">
                                        <div>GitHub</div>
                                        <ul class="list-unstyled">
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('General questions about openITCOCKPIT'); ?>
                                            </li>
                                            <li><i class="fa fa-minus text-muted"></i> <?= __('System issues'); ?></li>
                                            <li><i class="fa fa-minus text-muted"></i> <?= __('System crashes'); ?></li>
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('Unwanted behavior or bugs'); ?>
                                            </li>
                                            <li><i class="fa fa-minus text-muted"></i> <?= __('Errors with packages for your
                                        distribution'); ?>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="text">
                                        <div>Twitter</div>
                                        <ul class="list-unstyled">
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('Subscribe to get the very latest news about openITCOCKPIT'); ?>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="text">
                                        <div>Reddit</div>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-minus text-muted"></i> <?= __('News'); ?></li>
                                            <li><i class="fa fa-minus text-muted"></i> <?= __('Group discussions'); ?>
                                            </li>
                                            <li>
                                                <i class="fa fa-minus text-muted"></i> <?= __('Ask other openITCOCKPIT users'); ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="float-right">
                                        <a href="https://twitter.com/openITCOCKPIT"
                                           target="_blank"
                                           class="btn btn-default btn-sm">
                                            <i class="fab fa-twitter"></i>
                                            <?= __('twitter'); ?>
                                        </a>
                                        <a href="https://www.reddit.com/r/openitcockpit/"
                                           target="_blank"
                                           class="btn btn-default btn-sm">
                                            <i class="fab fa-reddit"></i>
                                            <?= __('Reddit'); ?>
                                        </a>
                                        <a href="https://github.com/it-novum/openITCOCKPIT/issues"
                                           target="_blank"
                                           class="btn btn-default btn-sm">
                                            <i class="fab fa-github"></i>
                                            <?= __('GitHub'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <iframe src="https://discord.com/widget?id=771364054924001290&theme=dark"
                                    height="500" allowtransparency="true" frameborder="0"
                                    sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                                    class="col-12"></iframe>
                        </div>
                    </div> <!-- end row -->

                </div>
            </div>
        </div>
    </div>
</div>

