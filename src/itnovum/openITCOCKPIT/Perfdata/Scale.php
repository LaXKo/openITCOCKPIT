<?php declare(strict_types=1);
// Copyright (C) <2024>  <it-novum GmbH>
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

namespace itnovum\openITCOCKPIT\Perfdata;

class Scale {
    /** @var float */
    public $min;
    /** @var float */
    public $max;
    /** @var string */
    public $type;

    public function __construct(?float $min = null, ?float $max = null, ?string $type = ScaleType::O) {
        $this->min = $min;
        $this->max = $max;
        $this->type = $type;
    }

    public function toArray(): array {
        return [
            'min'  => $this->min,
            'max'  => $this->max,
            'type' => $this->type
        ];
    }
}
