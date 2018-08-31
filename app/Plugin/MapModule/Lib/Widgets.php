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

namespace itnovum\openITCOCKPIT\MapModule\Widgets;


class Widgets {

    /**
     * @var array
     */
    private $ACL_PERMISSIONS = [];

    /**
     * Widgets constructor.
     * @param $ACL_PERMISSIONS
     */
    public function __construct($ACL_PERMISSIONS) {
        $this->ACL_PERMISSIONS = $ACL_PERMISSIONS;
    }

    /**
     * @return array
     */
    public function getAvailableWidgets() {
        $widgets = [];

        if (isset($this->ACL_PERMISSIONS['mapmodule']['maps']['index']) && isset($this->ACL_PERMISSIONS['mapmodule']['mapeditors']['view'])) {
            $widgets[] = [
                'type_id'   => 100,
                'title'     => __('Map'),
                'icon'      => 'fa-map-marker',
                'directive' => 'map-widget',
                'width'     => 12,
                'height'    => 25
            ];
        }

        return $widgets;
    }

}
