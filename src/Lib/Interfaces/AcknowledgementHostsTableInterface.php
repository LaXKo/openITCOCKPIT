<?php
// Copyright (C) <2018>  <it-novum GmbH>
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

namespace App\Lib\Interfaces;


use itnovum\openITCOCKPIT\Core\AcknowledgedHostConditions;
use itnovum\openITCOCKPIT\Database\PaginateOMat;

interface AcknowledgementHostsTableInterface {

    /**
     * @param AcknowledgedHostConditions $AcknowledgedHostConditions
     * @param PaginateOMat|null $PaginateOMat
     * @return array
     */
    public function getAcknowledgements(AcknowledgedHostConditions $AcknowledgedHostConditions, $PaginateOMat = null);

    /**
     * @param string $uuid
     * @return array
     */
    public function byHostUuid($uuid);

    /**
     * @param string $uuid
     * @return array
     */
    public function byUuid($uuid);

    /**
     * @param array $uuids
     * @return array
     */
    public function byUuids($uuids);
}
