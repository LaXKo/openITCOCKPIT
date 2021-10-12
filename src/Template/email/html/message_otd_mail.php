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
 */

echo $this->element('emails/style');

?>


<!-- ########## EMAIL CONTENT ############### -->

<body bgcolor="#FFFFFF">

<!-- HEADER -->
<table class="head-wrap alert-<?= $style; ?>">
    <tr>
        <td></td>
        <td class="header container">

            <div class="content">
                <table>
                    <tr>
                        <td><img src="cid:100" width="60"/></td>
                        <td align="right" class="message_otd_alert">
                            <h1><?= __('Message of the day') ?></h1>
                        </td>
                    </tr>
                </table>
            </div>

        </td>
        <td></td>
    </tr>
</table>
<!-- /HEADER -->


<!-- BODY -->
<table class="body-wrap">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <div class="content">
                <table>
                    <tr>
                        <td align="right">
                            <?= $date; ?>
                            <br>
                            <p class="info-date">
                                <?= ($expiration_duration) ? __('(Duration in days: {0})', $expiration_duration) : ''; ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5 class="title-border title-border-bottom-<?= $style; ?>">&nbsp;
                                <?= h($title) ?>
                            </h5>
                            <p>
                                <i><?= h($description) ?></i>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= $htmlContent; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
</table>
<table class="footer-wrap">
    <tr>
        <td></td>
        <td class="container">
            <div class="content">
                <table>
                    <tr>
                        <hr noshade width="560" size="3" align="left">
                        <br>
                        <td align="center">
                            <p>
                                <a href="https://openitcockpit.io/"><?php echo __('openITCOCKPIT'); ?></a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <p>
                                <?= date('l jS \of F Y') ?>
                            </p>
                            <p>
                                <?= date('H:i:s') ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
</table>
