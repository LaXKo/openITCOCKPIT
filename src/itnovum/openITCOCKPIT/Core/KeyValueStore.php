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

namespace itnovum\openITCOCKPIT\Core;


class KeyValueStore {

    /**
     * @var array
     */
    private $store = [];

    public function __construct() {
        $this->store = [];
    }

    /**
     * @param string|int $key
     * @return bool
     */
    public function has($key) {
        return isset($this->store[$key]);
    }

    /**
     * @param string|int $key
     * @param mixed $data
     */
    public function set($key, $data) {
        $this->store[$key] = $data;
    }

    /**
     * @param array $data
     */
    public function setArray($data){
        foreach($data as $key => $value){
            $this->set($key, $value);
        }
    }

    /**
     * @param string|int $key
     * @return mixed
     * @throws \Exception
     */
    public function get($key) {
        if ($this->has($key)) {
            return $this->store[$key];
        }

        throw new \Exception('Key not found in key value store');
    }

    /**
     * @param string|int $key
     */
    public function delete($key) {
        if ($this->has($key)) {
            unset($this->store[$key]);
        }
    }

}
