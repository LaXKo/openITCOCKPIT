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

use itnovum\openITCOCKPIT\Core\Views\HoststatusIcon;
use itnovum\openITCOCKPIT\Core\Views\Logo;

/**
 * @var \App\View\AppView $this
 * @var array $all_hosts
 * @var \itnovum\openITCOCKPIT\Core\ValueObjects\User $User
 */

$Logo = new Logo();
$css = \App\itnovum\openITCOCKPIT\Core\AngularJS\PdfAssets::getCssFiles();


$UserTime = $User->getUserTime();

?>
<head>

    <?php
    foreach ($css as $cssFile): ?>
        <link rel="stylesheet" type="text/css" href="<?php echo WWW_ROOT . $cssFile; ?>"/>
    <?php endforeach; ?>

</head>
<body>
<div class="well">
    <div class="row margin-top-10 font-lg no-padding">
        <div class="col-md-9 text-left padding-left-10">
            <i class="fa fa-desktop txt-color-blueDark padding-left-10"></i>
            <?php echo __('Hosts Overview'); ?>
        </div>
        <div class="col-md-3 text-left">
            <img src="<?php echo $Logo->getLogoPdfPath(); ?>" width="200"/>
        </div>
    </div>
    <div class="row padding-left-10 margin-top-10 font-sm">
        <div class="text-left padding-left-10">
            <i class="fa fa-calendar txt-color-blueDark"></i> <?php echo date('F d, Y H:i:s'); ?>
        </div>
    </div>
    <div class="row padding-left-10 margin-top-10 font-sm">
        <div class="text-left padding-left-10">
            <i class="fa fa-list-ol txt-color-blueDark"></i> <?php echo __('Number of Hosts: ' . sizeof($all_hosts)); ?>
        </div>
    </div>
    <div class="padding-top-10">
        <table id="" class="table table-striped table-bordered smart-form font-xs">
            <thead>
            <tr class="font-md">
                <th class="width-20"><?php echo __('Status'); ?></th>
                <th class="no-sort text-center width-20"><i class="fa fa-user fa-lg"></i></th>
                <th class="no-sort text-center width-20"><i class="fa fa-power-off fa-lg"></i></th>
                <th><?php echo __('Host'); ?></th>
                <th class="width-70"><?php echo __('Last state change'); ?></th>
                <th class="width-60"><?php echo __('Last check'); ?></th>
                <th><?php echo __('Output'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($all_hosts as $host): ?>
                <?php
                /** @var \itnovum\openITCOCKPIT\Core\Hoststatus $Hoststatus */
                $Hoststatus = $host['Hoststatus'];
                $HoststatusIcon = new HoststatusIcon($Hoststatus->currentState());
                ?>

                <tr>
                    <td class="text-center font-lg">
                        <?php
                        if ($Hoststatus->isFlapping()):
                            echo $Hoststatus->getHostFlappingIconColored();
                        else:
                            echo $HoststatusIcon->getPdfIcon();
                        endif;
                        ?>
                    </td>
                    <td class="text-center">
                        <?php if ($Hoststatus->isAcknowledged()): ?>
                            <i class="fa fa-user fa-lg"></i>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($Hoststatus->isInDowntime()): ?>
                            <i class="fa fa-power-off fa-lg"></i>
                        <?php endif; ?>
                    </td>
                    <td class="font-xs">
                        <?= h($host['Host']['name']); ?>
                    </td>
                    <?php if ($Hoststatus->isInMonitoring()): ?>
                        <td class="font-xs">
                            <?= h($UserTime->format($Hoststatus->getLastStateChange())) ?>
                        </td>
                        <td class="font-xs">
                            <?= h($UserTime->format($Hoststatus->getLastCheck())) ?>
                        </td>
                        <td class="font-xs">
                            <?= h($Hoststatus->getOutput()) ?>
                        </td>
                    <?php else: ?>
                        <td><?php echo __('n/a'); ?></td>
                        <td><?php echo __('n/a'); ?></td>
                        <td><?php echo __('n/a'); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($all_hosts)): ?>
                <div class="noMatch">
                    <center>
                        <span class="txt-color-red italic"><?php echo __('No entries match the selection'); ?></span>
                    </center>
                </div>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>