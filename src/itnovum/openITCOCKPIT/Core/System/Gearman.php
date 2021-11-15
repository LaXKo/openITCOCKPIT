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

namespace itnovum\openITCOCKPIT\Core\System;


use Cake\Core\Configure;

class Gearman {

    /**
     * @var array
     */
    private $config;

    /**
     * @var \GearmanClient
     */
    private $client;

    /**
     * Gearman constructor.
     */
    public function __construct() {
        Configure::load('gearman');
        $config = Configure::read('gearman');


        $this->config = $config;

        $this->client = new \GearmanClient();
        $this->client->addServer($this->config['address'], $this->config['port']);
    }

    public function send($task, $payload = []) {
        $payload['task'] = $task;
        $payload = serialize($payload);

        $result = $this->client->doNormal('oitc_gearman', $payload);

        $result = @unserialize($result);
        if (!is_array($result)) {
            return 'Corrupt data';
        }

        return $result;
    }

    public function sendBackground($task, $payload = []) {
        $payload['task'] = $task;
        $payload = serialize($payload);

        $result = $this->client->doBackground('oitc_gearman', $payload);

        $result = @unserialize($result);
        if (!is_array($result)) {
            return 'Corrupt data';
        }

        return $result;
    }

    /**
     * Sets the timeout for socket I/O activity.
     *
     * @link https://php.net/manual/en/gearmanclient.settimeout.php
     * @param int $timeout An interval of time in milliseconds
     * @return bool Always returns true
     */
    public function setTimeout($timeout) {
        return $this->client->setTimeout($timeout);
    }

    /**
     * @return bool
     */
    public function ping() {
        $client = new \GearmanClient();
        $client->addServer($this->config['address'], $this->config['port']);
        $client->setTimeout(5000);

        $result = @$client->ping(true);
        return $result;
    }

    /**
     * @param array $payload
     * @return string
     */
    public function toStatusnginCmdBackground(array $payload) {
        return $this->client->doBackground('statusngin_cmd', json_encode($payload));
    }

}
