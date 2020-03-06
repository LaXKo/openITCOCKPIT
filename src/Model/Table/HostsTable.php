<?php

namespace App\Model\Table;

use App\Lib\Traits\Cake2ResultTableTrait;
use App\Lib\Traits\CustomValidationTrait;
use App\Lib\Traits\PaginationAndScrollIndexTrait;
use App\Lib\Traits\PluginManagerTableTrait;
use App\Model\Entity\Changelog;
use App\Model\Entity\Host;
use App\Model\Entity\Hostdependency;
use App\Model\Entity\Hostescalation;
use Cake\Database\Expression\Comparison;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use itnovum\openITCOCKPIT\Core\HostConditions;
use itnovum\openITCOCKPIT\Core\ValueObjects\User;
use itnovum\openITCOCKPIT\Database\PaginateOMat;
use itnovum\openITCOCKPIT\Filter\HostFilter;

/**
 * Hosts Model
 *
 * @property \App\Model\Table\ContainersTable|\Cake\ORM\Association\BelongsTo $Containers
 * @property \App\Model\Table\HosttemplatesTable|\Cake\ORM\Association\BelongsTo $Hosttemplates
 * @property \App\Model\Table\CommandsTable|\Cake\ORM\Association\BelongsTo $Commands
 * @property \App\Model\Table\EventhandlerCommandsTable|\Cake\ORM\Association\BelongsTo $EventhandlerCommands
 * @property \App\Model\Table\TimeperiodsTable|\Cake\ORM\Association\BelongsTo $Timeperiods
 * @property \App\Model\Table\TimeperiodsTable|\Cake\ORM\Association\BelongsTo $CheckPeriods
 * @property \App\Model\Table\TimeperiodsTable|\Cake\ORM\Association\BelongsTo $NotifyPeriods
 * @property \App\Model\Table\SatellitesTable|\Cake\ORM\Association\BelongsTo $Satellites
 * @property \App\Model\Table\ContactgroupsToHostsTable|\Cake\ORM\Association\HasMany $ContactgroupsToHosts
 * @property \App\Model\Table\ContactsToHostsTable|\Cake\ORM\Association\HasMany $ContactsToHosts
 * @property \App\Model\Table\DeletedHostsTable|\Cake\ORM\Association\HasMany $DeletedHosts
 * @property \App\Model\Table\ServicesTable|\Cake\ORM\Association\HasMany $Services
 *
 * @method \App\Model\Entity\Host get($primaryKey, $options = [])
 * @method \App\Model\Entity\Host newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Host[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Host|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Host|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Host patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Host[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Host findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HostsTable extends Table {

    use PaginationAndScrollIndexTrait;
    use Cake2ResultTableTrait;
    use CustomValidationTrait;
    use PluginManagerTableTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('hosts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('HostsToContainersSharing', [
            'className'        => 'Containers',
            'joinTable'        => 'hosts_to_containers',
            'foreignKey'       => 'host_id',
            'targetForeignKey' => 'container_id',
            'saveStrategy'     => 'replace'
        ]);

        $this->belongsTo('Containers', [
            'foreignKey' => 'container_id',
            'joinType'   => 'INNER'
        ]);

        $this->belongsToMany('Contactgroups', [
            'className'        => 'Contactgroups',
            'foreignKey'       => 'host_id',
            'targetForeignKey' => 'contactgroup_id',
            'joinTable'        => 'contactgroups_to_hosts',
            'saveStrategy'     => 'replace'
        ])->setDependent(true);

        $this->belongsToMany('Contacts', [
            'className'        => 'Contacts',
            'foreignKey'       => 'host_id',
            'targetForeignKey' => 'contact_id',
            'joinTable'        => 'contacts_to_hosts',
            'saveStrategy'     => 'replace'
        ])->setDependent(true);

        $this->belongsToMany('Hostgroups', [
            'className'        => 'Hostgroups',
            'foreignKey'       => 'host_id',
            'targetForeignKey' => 'hostgroup_id',
            'joinTable'        => 'hosts_to_hostgroups',
            'saveStrategy'     => 'replace'
        ])->setDependent(true);


        $this->belongsToMany('Parenthosts', [
            'className'        => 'Hosts',
            'foreignKey'       => 'host_id',
            'targetForeignKey' => 'parenthost_id',
            'joinTable'        => 'hosts_to_parenthosts',
            'saveStrategy'     => 'replace'
        ])->setDependent(true);

        $this->belongsTo('Hosttemplates', [
            'foreignKey' => 'hosttemplate_id',
            'joinType'   => 'INNER'
        ]);

        $this->belongsTo('CheckPeriod', [
            'className'  => 'Timeperiods',
            'foreignKey' => 'check_period_id',
            'joinType'   => 'LEFT OUTER'
        ]);

        $this->belongsTo('NotifyPeriod', [
            'className'  => 'Timeperiods',
            'foreignKey' => 'notify_period_id',
            'joinType'   => 'LEFT OUTER'
        ]);

        $this->belongsTo('CheckCommand', [
            'className'  => 'Commands',
            'foreignKey' => 'command_id',
            'joinType'   => 'INNER'
        ]);

        $this->hasMany('Customvariables', [
            'conditions'   => [
                'objecttype_id' => OBJECT_HOST
            ],
            'foreignKey'   => 'object_id',
            'saveStrategy' => 'replace'
        ])->setDependent(true);

        $this->hasMany('Hostcommandargumentvalues', [
            'saveStrategy' => 'replace'
        ])->setDependent(true);

        $this->hasMany('Services', [
            'foreignKey' => 'host_id',
        ])->setDependent(true);

        $this->hasMany('HostescalationsHostMemberships', [
            'foreignKey' => 'host_id'
        ]);

        $this->hasMany('HostdependenciesHostMemberships', [
            'foreignKey' => 'host_id'
        ]);

        $this->hasOne('Agentconfigs', [
            'foreignKey' => 'host_id',
        ]);

        $this->hasOne('Agenthostscache', [
            'foreignKey' => 'hostuuid',
            'bindingKey' => 'uuid'
        ]);

        $this->hasOne('Agentconnector', [
            'foreignKey' => 'hostuuid',
            'bindingKey' => 'uuid'
        ]);

    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('hosttemplate_id')
            ->requirePresence('hosttemplate_id', 'create')
            ->allowEmptyString('hosttemplate_id', null, false);

        $validator
            ->scalar('uuid')
            ->maxLength('uuid', 37)
            ->requirePresence('uuid', 'create')
            ->allowEmptyString('uuid', null, false)
            ->add('uuid', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->allowEmptyString('name', null, false);

        $validator
            ->scalar('address')
            ->maxLength('address', 255)
            ->requirePresence('address', 'create')
            ->allowEmptyString('address', null, false);

        $validator
            ->allowEmptyString('description', null, true);

        $validator
            ->integer('priority')
            ->requirePresence('priority', 'create')
            ->range('priority', [1, 5], __('This value must be between 1 and 5'))
            ->allowEmptyString('priority');

        $validator
            ->integer('container_id')
            ->requirePresence('container_id', 'create')
            ->allowEmptyString('container_id', null, false)
            ->greaterThanOrEqual('container_id', 1);

        $validator
            ->integer('max_check_attempts')
            ->requirePresence('max_check_attempts', 'create')
            ->greaterThanOrEqual('max_check_attempts', 1, __('This value need to be at least 1'))
            ->allowEmptyString('max_check_attempts', null, true);

        $validator
            ->numeric('notification_interval')
            ->requirePresence('notification_interval', 'create')
            ->greaterThanOrEqual('notification_interval', 0, __('This value need to be at least 0'))
            ->allowEmptyString('notification_interval', null, true);

        $validator
            ->integer('check_interval')
            ->requirePresence('check_interval', 'create')
            ->greaterThanOrEqual('check_interval', 1, __('This value need to be at least 1'))
            ->allowEmptyString('check_interval', null, true);

        $validator
            ->integer('retry_interval')
            ->requirePresence('retry_interval', 'create')
            ->greaterThanOrEqual('retry_interval', 1, __('This value need to be at least 1'))
            ->allowEmptyString('retry_interval', null, true);

        $validator
            ->integer('check_period_id')
            ->requirePresence('check_period_id', 'create')
            ->greaterThan('check_period_id', 0, __('Please select a check period'))
            ->allowEmptyString('check_period_id', null, true);

        $validator
            ->integer('command_id')
            ->requirePresence('command_id', 'create')
            ->greaterThan('command_id', 0, __('Please select a check command'))
            ->allowEmptyString('command_id', null, true);

        $validator
            ->integer('notify_period_id')
            ->requirePresence('notify_period_id', 'create')
            ->greaterThan('notify_period_id', 0, __('Please select a notify period'))
            ->allowEmptyString('notify_period_id', null, true);

        $validator
            ->boolean('notify_on_recovery')
            ->requirePresence('notify_on_recovery', 'create')
            ->allowEmptyString('notify_on_recovery', null, true)
            ->add('notify_on_recovery', 'custom', [
                'rule'    => [$this, 'checkNotificationOptionsHost'], //\App\Lib\Traits\CustomValidationTrait
                'message' => __('You must specify at least one notification option.')
            ]);

        $validator
            ->boolean('notify_on_down')
            ->requirePresence('notify_on_down', 'create')
            ->allowEmptyString('notify_on_down', null, true)
            ->add('notify_on_down', 'custom', [
                'rule'    => [$this, 'checkNotificationOptionsHost'], //\App\Lib\Traits\CustomValidationTrait
                'message' => __('You must specify at least one notification option.')
            ]);

        $validator
            ->boolean('notify_on_unreachable')
            ->requirePresence('notify_on_unreachable', 'create')
            ->allowEmptyString('notify_on_unreachable', null, true)
            ->add('notify_on_unreachable', 'custom', [
                'rule'    => [$this, 'checkNotificationOptionsHost'], //\App\Lib\Traits\CustomValidationTrait
                'message' => __('You must specify at least one notification option.')
            ]);

        $validator
            ->boolean('notify_on_flapping')
            ->requirePresence('notify_on_flapping', 'create')
            ->allowEmptyString('notify_on_flapping', null, true)
            ->add('notify_on_flapping', 'custom', [
                'rule'    => [$this, 'checkNotificationOptionsHost'], //\App\Lib\Traits\CustomValidationTrait
                'message' => __('You must specify at least one notification option.')
            ]);

        $validator
            ->boolean('notify_on_downtime')
            ->requirePresence('notify_on_downtime', 'create')
            ->allowEmptyString('notify_on_downtime', null, true)
            ->add('notify_on_downtime', 'custom', [
                'rule'    => [$this, 'checkNotificationOptionsHost'], //\App\Lib\Traits\CustomValidationTrait
                'message' => __('You must specify at least one notification option.')
            ]);

        $validator
            ->boolean('flap_detection_enabled')
            ->requirePresence('flap_detection_enabled', 'create')
            ->allowEmptyString('flap_detection_enabled', null, true);

        $validator
            ->allowEmptyString('flap_detection_on_up', __('You must specify at least one flap detection option.'), function ($context) {
                return $this->checkFlapDetectionOptionsHost(null, $context);
            })
            ->add('flap_detection_on_up', 'custom', [
                'rule'    => [$this, 'checkFlapDetectionOptionsHost'], //\App\Lib\Traits\CustomValidationTrait
                'message' => __('You must specify at least one flap detection option.')
            ]);

        $validator
            ->allowEmptyString('flap_detection_on_down', __('You must specify at least one flap detection option.'), function ($context) {
                return $this->checkFlapDetectionOptionsHost(null, $context);
            })
            ->add('flap_detection_on_down', 'custom', [
                'rule'    => [$this, 'checkFlapDetectionOptionsHost'], //\App\Lib\Traits\CustomValidationTrait
                'message' => __('You must specify at least one flap detection option.')
            ]);

        $validator
            ->allowEmptyString('flap_detection_on_unreachable', __('You must specify at least one flap detection option.'), function ($context) {
                return $this->checkFlapDetectionOptionsHost(null, $context);
            })
            ->add('flap_detection_on_unreachable', 'custom', [
                'rule'    => [$this, 'checkFlapDetectionOptionsHost'], //\App\Lib\Traits\CustomValidationTrait
                'message' => __('You must specify at least one flap detection option.')
            ]);


        $validator
            ->numeric('low_flap_threshold')
            ->allowEmptyString('low_flap_threshold');

        $validator
            ->numeric('high_flap_threshold')
            ->allowEmptyString('high_flap_threshold');

        $validator
            ->boolean('process_performance_data')
            ->requirePresence('process_performance_data', false)
            ->allowEmptyString('process_performance_data', null, true);

        $validator
            ->boolean('freshness_checks_enabled')
            ->requirePresence('freshness_checks_enabled', false)
            ->allowEmptyString('freshness_checks_enabled', null, true);

        $validator
            ->integer('freshness_threshold')
            ->allowEmptyString('freshness_threshold');

        $validator
            ->boolean('passive_checks_enabled')
            ->allowEmptyString('passive_checks_enabled');

        $validator
            ->boolean('event_handler_enabled')
            ->allowEmptyString('event_handler_enabled');

        $validator
            ->boolean('active_checks_enabled')
            ->requirePresence('active_checks_enabled', 'create')
            ->allowEmptyString('active_checks_enabled', null, true);

        $validator
            ->scalar('notes')
            ->requirePresence('notes', false)
            ->allowEmptyString('notes', null, true)
            ->maxLength('notes', 255);

        $validator
            ->scalar('tags')
            ->requirePresence('tags', false)
            ->allowEmptyString('tags', null, true)
            ->maxLength('tags', 255);

        $validator
            ->scalar('host_url')
            ->requirePresence('host_url', false)
            ->allowEmptyString('host_url', null, true)
            ->maxLength('host_url', 255);


        $validator
            ->allowEmptyString('customvariables', null, true)
            ->add('customvariables', 'custom', [
                'rule'    => [$this, 'checkMacroNames'], //\App\Lib\Traits\CustomValidationTrait
                'message' => _('Macro name needs to be unique')
            ]);

        $validator
            ->boolean('own_contacts')
            ->requirePresence('own_contacts', 'create')
            ->allowEmptyString('own_contacts', null, false);

        $validator
            ->boolean('own_contactgroups')
            ->requirePresence('own_contactgroups', 'create')
            ->allowEmptyString('own_contactgroups', null, false);

        $validator
            ->boolean('own_customvariables')
            ->requirePresence('own_customvariables', 'create')
            ->allowEmptyString('own_customvariables', null, false);

        $validator
            ->integer('host_type')
            ->requirePresence('host_type', 'create')
            ->allowEmptyString('host_type', null, false);

        $validator
            ->boolean('disabled')
            ->allowEmptyString('disabled');

        $validator
            ->integer('usage_flag')
            ->requirePresence('usage_flag', 'create')
            ->allowEmptyString('usage_flag', null, false);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker {
        $rules->add($rules->isUnique(['uuid']));

        return $rules;
    }

    /**
     * @param int $id
     * @return array|Host|null
     */
    public function getHostById($id) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $id
            ])
            ->contain('HostsToContainersSharing')
            ->first();
        return $query;
    }

    /**
     * @param string $uuid
     * @param bool $enableHydration
     * @return array|Host
     */
    public function getHostByUuid($uuid, $enableHydration = true) {
        $query = $this->find()
            ->where([
                'Hosts.uuid' => $uuid
            ])
            ->contain('HostsToContainersSharing')
            ->enableHydration($enableHydration)
            ->firstOrFail();
        return $query;
    }

    /**
     * @param string $uuid
     * @param bool $enableHydration
     * @return array|Host
     */
    public function getHostWithServicesByUuid($uuid, $enableHydration = true) {
        $query = $this->find()
            ->where([
                'Hosts.uuid' => $uuid
            ])
            ->contain([
                'HostsToContainersSharing',
                'Services'
            ])
            ->enableHydration($enableHydration)
            ->firstOrFail();
        return $query;
    }

    /**
     * @param string $uuid
     * @param bool $enableHydration
     * @return array|Host
     */
    public function getHostsWithServicesByIdsForMapeditor($ids, $enableHydration = true) {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $query = $this->find()
            ->where([
                'Hosts.id IN'    => $ids,
                'Hosts.disabled' => 0,
            ])
            ->contain([
                'HostsToContainersSharing',
                'Services' => function (Query $q) {
                    return $q->where([
                        'Services.disabled' => 0
                    ]);
                }
            ])
            ->enableHydration($enableHydration);

        $result = $query->all();
        if (empty($result)) {
            return [];
        }
        return $result->toArray();
    }

    /**
     * @param string $uuid
     * @param bool $enableHydration
     * @return array|Host
     */
    public function getHostsWithServicesByIdsForMapsumary($ids, $enableHydration = true) {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $query = $this->find()
            ->where([
                'Hosts.id IN'    => $ids,
                'Hosts.disabled' => 0,
            ])
            ->contain([
                'HostsToContainersSharing',
                'Services' => function (Query $q) {
                    return $q
                        ->join([
                            [
                                'table'      => 'servicetemplates',
                                'type'       => 'INNER',
                                'alias'      => 'Servicetemplates',
                                'conditions' => 'Servicetemplates.id = Services.servicetemplate_id',
                            ],
                        ])
                        ->select([
                            'Services.id',
                            'Services.name',
                            'Services.uuid',
                            'Services.host_id',
                            'Servicetemplates.name'
                        ])
                        ->where([
                            'Services.disabled' => 0
                        ]);
                }
            ])
            ->select([
                'Hosts.id',
                'Hosts.uuid',
                'Hosts.name'
            ])
            ->enableHydration($enableHydration);

        $result = $query->all();
        if (empty($result)) {
            return [];
        }
        return $result->toArray();
    }

    /**
     * @param int $id
     * @return array|Host|null
     */
    public function getHostByIdForPermissionCheck($id) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.uuid',
                'Hosts.name',
                'Hosts.address',
                'Hosts.container_id'
            ])
            ->where([
                'Hosts.id' => $id
            ])
            ->contain('HostsToContainersSharing')
            ->first();
        return $query;
    }

    /**
     * @param int $id
     * @return array|Host|null
     */
    public function getHostByIdWithHosttemplate($id) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'HostsToContainersSharing',
                'Hosttemplates'
            ])
            ->first();
        return $query;
    }

    /**
     * @param int $id
     * @return array|Host|null
     */
    public function getHostByIdWithHosttemplateForEditDetails($id) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'HostsToContainersSharing',
                'Contacts'      => [
                    'Containers' => [
                        'fields' => [
                            'ContactsToContainers.contact_id',
                            'Containers.id'
                        ]
                    ],
                    'fields'     => [
                        'ContactsToHosts.host_id',
                        'Contacts.id'
                    ]
                ],
                'Contactgroups' => [
                    'Containers' => [
                        'fields' => [
                            'Containers.parent_id'
                        ]
                    ],
                    'fields'     => [
                        'ContactgroupsToHosts.host_id',
                        'Contactgroups.id'
                    ]
                ],
                'Hosttemplates' => [
                    'Contacts'      => [
                        'Containers' => [
                            'fields' => [
                                'ContactsToContainers.contact_id',
                                'Containers.id'
                            ]
                        ],
                        'fields'     => [
                            'ContactsToHosttemplates.hosttemplate_id',
                            'Contacts.id'
                        ]
                    ],
                    'Contactgroups' => [
                        'Containers' => [
                            'fields' => [
                                'Containers.parent_id'
                            ]
                        ],
                        'fields'     => [
                            'ContactgroupsToHosttemplates.hosttemplate_id',
                            'Contactgroups.id'
                        ]
                    ]
                ]
            ])
            ->first();
        return $query;
    }

    /**
     * @param int|array $ids
     * @return array
     */
    public function getHostsByIds($ids, $useHydration = true) {
        if (empty($ids)) {
            return [];
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $query = $this->find()
            ->where([
                'Hosts.id IN' => $ids
            ])
            ->contain('HostsToContainersSharing')
            ->enableHydration($useHydration)
            ->all();

        $result = $query->toArray();
        if (empty($result)) {
            return [];
        }

        return $result;
    }

    /**
     * @param int $hosttemplateId
     * @return array
     */
    public function getHostPrimaryContainerIdsByHosttemplateId($hosttemplateId) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.container_id',
                'Hosts.hosttemplate_id'
            ])
            ->where([
                'Hosts.hosttemplate_id' => $hosttemplateId
            ])
            ->disableHydration()
            ->all();

        $query = $query->toArray();

        if (empty($query)) {
            return [];
        }

        $result = [];
        foreach ($query as $row) {
            $result[$row['id']] = (int)$row['container_id'];
        }

        return $result;
    }

    /**
     * @param int $hosttemplateId
     * @param array $MY_RIGHTS
     * @return array
     */
    public function getHostsForHosttemplateUsedBy($hosttemplateId, $MY_RIGHTS = [], $includeDisabled = false) {
        $query = $this->find('all');
        $query->select([
            'Hosts.id',
            'Hosts.container_id',
            'Hosts.uuid',
            'Hosts.name',
            'Hosts.address',
            'Hosts.disabled'
        ]);

        $where = [
            'Hosts.hosttemplate_id' => $hosttemplateId
        ];
        if ($includeDisabled === false) {
            $where['Hosts.disabled'] = 0;
        }

        $query->where($where);
        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain('HostsToContainersSharing');
        $query->disableHydration();
        $query->group(['Hosts.id']);
        $query->order([
            'Hosts.name' => 'asc'
        ]);

        $result = $query->toArray();
        if (empty($result)) {
            return [];
        }

        return $result;
    }

    /**
     * @param HostFilter $HostFilter
     * @param HostConditions $HostConditions
     * @param null|PaginateOMat $PaginateOMat
     * @return array
     */
    public function getHostsIndex(HostFilter $HostFilter, HostConditions $HostConditions, $PaginateOMat = null) {
        $MY_RIGHTS = $HostConditions->getContainerIds();

        $query = $this->find('all');
        $query->select([
            'Hosts.id',
            'Hosts.uuid',
            'Hosts.name',
            'Hosts.description',
            'Hosts.active_checks_enabled',
            'Hosts.address',
            'Hosts.satellite_id',
            'Hosts.container_id',
            'Hosts.tags',

            //'keywords'     => 'IF((Hosts.tags IS NULL OR Hosts.tags=""), Hosttemplates.tags, Hosts.tags)',
            //'not_keywords' => 'IF((Hosts.tags IS NULL OR Hosts.tags=""), Hosttemplates.tags, Hosts.tags)',

            'Hoststatus.current_state',
            'Hoststatus.last_check',
            'Hoststatus.next_check',
            'Hoststatus.last_hard_state_change',
            'Hoststatus.last_state_change',
            'Hoststatus.output',
            'Hoststatus.scheduled_downtime_depth',
            'Hoststatus.active_checks_enabled',
            'Hoststatus.state_type',
            'Hoststatus.is_flapping',
            'Hoststatus.problem_has_been_acknowledged',
            'Hoststatus.acknowledgement_type'
        ]);

        $query->join([
            'a' => [
                'table'      => 'nagios_objects',
                'type'       => 'INNER',
                'alias'      => 'HostObject',
                'conditions' => 'Hosts.uuid = HostObject.name1 AND HostObject.objecttype_id = 1'
            ],
            'b' => [
                'table'      => 'nagios_hoststatus',
                'type'       => 'LEFT OUTER',
                'alias'      => 'Hoststatus',
                'conditions' => 'Hoststatus.host_object_id = HostObject.object_id',
            ]
        ]);

        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }

        $query->contain([
            'HostsToContainersSharing',
            'Hosttemplates' => [
                'fields' => [
                    'Hosttemplates.id',
                    'Hosttemplates.uuid',
                    'Hosttemplates.name',
                    'Hosttemplates.description',
                    'Hosttemplates.active_checks_enabled',
                    'Hosttemplates.tags'
                ]
            ]
        ]);

        $where = $HostFilter->indexFilter();
        $where['Hosts.disabled'] = (int)$HostConditions->includeDisabled();
        if ($HostConditions->getHostIds()) {
            $hostIds = $HostConditions->getHostIds();
            if (!is_array($hostIds)) {
                $hostIds = [$hostIds];
            }

            $where['Hosts.id IN'] = $hostIds;
        }

        if (isset($where['Hosts.keywords rlike'])) {
            $where[] = new Comparison(
                'IF((Hosts.tags IS NULL OR Hosts.tags=""), Hosttemplates.tags, Hosts.tags)',
                $where['Hosts.keywords rlike'],
                'string',
                'RLIKE'
            );
            unset($where['Hosts.keywords rlike']);
        }

        if (isset($where['Hosts.not_keywords not rlike'])) {
            $where[] = new Comparison(
                'IF((Hosts.tags IS NULL OR Hosts.tags=""), Hosttemplates.tags, Hosts.tags)',
                $where['Hosts.not_keywords not rlike'],
                'string',
                'NOT RLIKE'
            );
            unset($where['Hosts.not_keywords not rlike']);
        }


        $query->where($where);

        $query->disableHydration();
        $query->group(['Hosts.id']);
        $query->order($HostFilter->getOrderForPaginator('Hoststatus.current_state', 'desc'));

        if ($PaginateOMat === null) {
            //Just execute query
            $result = $this->formatResultAsCake2($query->toArray(), false);
        } else {
            if ($PaginateOMat->useScroll()) {
                $result = $this->scroll($query, $PaginateOMat->getHandler(), false);
            } else {
                $result = $this->paginate($query, $PaginateOMat->getHandler(), false);
            }
        }
        return $result;
    }

    /**
     * @param HostFilter $HostFilter
     * @param HostConditions $HostConditions
     * @param null|PaginateOMat $PaginateOMat
     * @return array
     */
    public function getHostsNotMonitored(HostFilter $HostFilter, HostConditions $HostConditions, $PaginateOMat = null) {
        $MY_RIGHTS = $HostConditions->getContainerIds();

        $query = $this->find('all');
        $query->select([
            'Hosts.id',
            'Hosts.uuid',
            'Hosts.name',
            'Hosts.description',
            'Hosts.address',
            'Hosts.satellite_id',
            'Hosts.container_id'
        ]);

        $query->join([
            'a' => [
                'table'      => 'nagios_objects',
                'type'       => 'LEFT OUTER',
                'alias'      => 'HostObject',
                'conditions' => 'Hosts.uuid = HostObject.name1 AND HostObject.objecttype_id = 1'
            ]
        ]);

        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }

        $query->contain([
            'HostsToContainersSharing',
            'Hosttemplates' => [
                'fields' => [
                    'Hosttemplates.id',
                    'Hosttemplates.uuid',
                    'Hosttemplates.name',
                    'Hosttemplates.description',
                    'Hosttemplates.active_checks_enabled',
                ]
            ]

        ]);

        $where = $HostFilter->disabledFilter();
        $where['Hosts.disabled'] = (int)$HostConditions->includeDisabled();
        $where[] = 'HostObject.name1 IS NULL';
        $query->where($where);

        $query->disableHydration();
        $query->group(['Hosts.id']);
        $query->order($HostFilter->getOrderForPaginator('Hosts.name', 'asc'));

        if ($PaginateOMat === null) {
            //Just execute query
            $result = $this->formatResultAsCake2($query->toArray(), false);
        } else {
            if ($PaginateOMat->useScroll()) {
                $result = $this->scroll($query, $PaginateOMat->getHandler(), false);
            } else {
                $result = $this->paginate($query, $PaginateOMat->getHandler(), false);
            }
        }
        return $result;
    }


    /**
     * @param HostFilter $HostFilter
     * @param HostConditions $HostConditions
     * @param null|PaginateOMat $PaginateOMat
     * @return array
     */
    public function getHostsDisabled(HostFilter $HostFilter, HostConditions $HostConditions, $PaginateOMat = null) {
        $MY_RIGHTS = $HostConditions->getContainerIds();

        $query = $this->find('all');
        $query->select([
            'Hosts.id',
            'Hosts.uuid',
            'Hosts.name',
            'Hosts.description',
            'Hosts.address',
            'Hosts.satellite_id',
            'Hosts.container_id'
        ]);

        $where = $HostFilter->disabledFilter();
        $where['Hosts.disabled'] = (int)$HostConditions->includeDisabled();

        $query->where($where);
        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing',
            'Hosttemplates' => [
                'fields' => [
                    'Hosttemplates.id',
                    'Hosttemplates.name'
                ]
            ]

        ]);
        $query->disableHydration();
        $query->group(['Hosts.id']);
        $query->order($HostFilter->getOrderForPaginator('Hosts.name', 'asc'));

        if ($PaginateOMat === null) {
            //Just execute query
            $result = $this->formatResultAsCake2($query->toArray(), false);
        } else {
            if ($PaginateOMat->useScroll()) {
                $result = $this->scroll($query, $PaginateOMat->getHandler(), false);
            } else {
                $result = $this->paginate($query, $PaginateOMat->getHandler(), false);
            }
        }
        return $result;
    }

    /**
     * @param HostConditions $HostConditions
     * @param null|PaginateOMat $PaginateOMat
     * @return array
     */
    public function getHostsByHostConditions(HostConditions $HostConditions, $PaginateOMat = null) {
        $MY_RIGHTS = $HostConditions->getContainerIds();

        $query = $this->find('all');
        $query->select([
            'Hosts.id',
            'Hosts.uuid',
            'Hosts.name',
            'Hosts.description',
            'Hosts.active_checks_enabled',
            'Hosts.address',
            'Hosts.satellite_id',
            'Hosts.container_id',
            'Hosts.tags',
        ]);

        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing'
        ]);

        $where = $HostConditions->getWhereForFind();

        if ($HostConditions->getHostIds()) {
            $hostIds = $HostConditions->getHostIds();
            if (!is_array($hostIds)) {
                $hostIds = [$hostIds];
            }

            $where['Hosts.id IN'] = $hostIds;
        }

        if (isset($where['Hosts.keywords rlike'])) {
            $where[] = new Comparison(
                'IF((Hosts.tags IS NULL OR Hosts.tags=""), Hosttemplates.tags, Hosts.tags)',
                $where['Hosts.keywords rlike'],
                'string',
                'RLIKE'
            );
            unset($where['Hosts.keywords rlike']);
        }

        if (isset($where['Hosts.not_keywords not rlike'])) {
            $where[] = new Comparison(
                'IF((Hosts.tags IS NULL OR Hosts.tags=""), Hosttemplates.tags, Hosts.tags)',
                $where['Hosts.not_keywords not rlike'],
                'string',
                'NOT RLIKE'
            );
            unset($where['Hosts.not_keywords not rlike']);
        }


        $query->where($where);

        $query->disableHydration();
        $query->group(['Hosts.id']);
        if (!empty($HostConditions->getOrder())) {
            $query->order($HostConditions->getOrder());
        }

        if ($PaginateOMat === null) {
            //Just execute query
            $result = $this->formatResultAsCake2($query->toArray(), false);
        } else {
            if ($PaginateOMat->useScroll()) {
                $result = $this->scroll($query, $PaginateOMat->getHandler(), false);
            } else {
                $result = $this->paginate($query, $PaginateOMat->getHandler(), false);
            }
        }
        return $result;
    }

    /**
     * @param HostConditions $HostConditions
     * @param null|PaginateOMat $PaginateOMat
     * @return array
     */
    public function getHostsByHostConditionsWithServices(HostConditions $HostConditions, $PaginateOMat = null) {
        $MY_RIGHTS = $HostConditions->getContainerIds();

        $query = $this->find('all');
        $query->select([
            'Hosts.id',
            'Hosts.uuid',
            'Hosts.name',
            'Hosts.description',
            'Hosts.active_checks_enabled',
            'Hosts.address',
            'Hosts.satellite_id',
            'Hosts.container_id',
            'Hosts.tags',
        ]);

        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing'
        ]);

        $where = $HostConditions->getWhereForFind();

        if ($HostConditions->getHostIds()) {
            $hostIds = $HostConditions->getHostIds();
            if (!is_array($hostIds)) {
                $hostIds = [$hostIds];
            }

            $where['Hosts.id IN'] = $hostIds;
        }

        if (isset($where['Hosts.keywords rlike'])) {
            $where[] = new Comparison(
                'IF((Hosts.tags IS NULL OR Hosts.tags=""), Hosttemplates.tags, Hosts.tags)',
                $where['Hosts.keywords rlike'],
                'string',
                'RLIKE'
            );
            unset($where['Hosts.keywords rlike']);
        }

        if (isset($where['Hosts.not_keywords not rlike'])) {
            $where[] = new Comparison(
                'IF((Hosts.tags IS NULL OR Hosts.tags=""), Hosttemplates.tags, Hosts.tags)',
                $where['Hosts.not_keywords not rlike'],
                'string',
                'NOT RLIKE'
            );
            unset($where['Hosts.not_keywords not rlike']);
        }


        $query->where($where);

        $query->disableHydration();
        $query->group(['Hosts.id']);
        $query->order([
            'Hosts.name' => 'asc'
        ]);

        if ($PaginateOMat === null) {
            //Just execute query
            $result = $this->formatResultAsCake2($query->toArray(), false);
        } else {
            if ($PaginateOMat->useScroll()) {
                $result = $this->scroll($query, $PaginateOMat->getHandler(), false);
            } else {
                $result = $this->paginate($query, $PaginateOMat->getHandler(), false);
            }
        }
        return $result;
    }

    /**
     * @param array $containerIds
     * @param string $type
     * @param string $index
     * @param array $where
     * @return array
     */
    public function getHostsByContainerId($containerIds = [], $type = 'all', $index = 'id', $where = []) {
        if (!is_array($containerIds)) {
            $containerIds = [$containerIds];
        }
        $containerIds = array_unique($containerIds);

        $_where = [
            'Hosts.disabled IN' => [0]
        ];

        $where = Hash::merge($_where, $where);

        $query = $this->find();
        $query->select([
            'Hosts.' . $index,
            'Hosts.name'
        ]);

        $query->where($where);
        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $containerIds
            ]);
        }
        $query->disableHydration();
        $query->group(['Hosts.id']);
        $query->order([
            'Hosts.name' => 'asc'
        ]);

        $result = $query->toArray();
        if (empty($result)) {
            return [];
        }

        if ($type === 'all') {
            return $result;
        }

        $list = [];
        foreach ($result as $row) {
            $list[$row[$index]] = $row['name'];
        }

        return $list;
    }

    /**
     * @param array $containerIds
     * @return array
     */
    public function getHostsByContainerIdForDelete($containerIds = []) {
        if (!is_array($containerIds)) {
            $containerIds = [$containerIds];
        }
        $containerIds = array_unique($containerIds);

        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.name'
            ])->where([
                'Hosts.container_id IN' => $containerIds
            ])->disableHydration();

        $result = $query->toArray();
        if (empty($result)) {
            return [];
        }
        return $result;
    }

    /**
     * @param array $dataToParse
     * @return array
     */
    public function resolveDataForChangelog($dataToParse = []) {
        $extDataForChangelog = [
            'Contact'      => [],
            'Contactgroup' => [],
            'CheckPeriod'  => [],
            'NotifyPeriod' => [],
            'CheckCommand' => [],
            'Hostgroup'    => [],
            'Hosttemplate' => [],
            'Parenthost'   => []
        ];

        /** @var $CommandsTable CommandsTable */
        $CommandsTable = TableRegistry::getTableLocator()->get('Commands');
        /** @var $ContactsTable ContactsTable */
        $ContactsTable = TableRegistry::getTableLocator()->get('Contacts');
        /** @var $ContactgroupsTable ContactgroupsTable */
        $ContactgroupsTable = TableRegistry::getTableLocator()->get('Contactgroups');
        /** @var $TimeperiodsTable TimeperiodsTable */
        $TimeperiodsTable = TableRegistry::getTableLocator()->get('Timeperiods');
        /** @var $HostgroupsTable HostgroupsTable */
        $HostgroupsTable = TableRegistry::getTableLocator()->get('Hostgroups');
        /** @var $HosttemplatesTable HosttemplatesTable */
        $HosttemplatesTable = TableRegistry::getTableLocator()->get('Hosttemplates');


        if (!empty($dataToParse['Host']['contacts']['_ids'])) {
            foreach ($ContactsTable->getContactsAsList($dataToParse['Host']['contacts']['_ids']) as $contactId => $contactName) {
                $extDataForChangelog['Contact'][] = [
                    'id'   => $contactId,
                    'name' => $contactName
                ];
            }
        }

        if (!empty($dataToParse['Host']['contactgroups']['_ids'])) {
            foreach ($ContactgroupsTable->getContactgroupsAsList($dataToParse['Host']['contactgroups']['_ids']) as $contactgroupId => $contactgroupName) {
                $extDataForChangelog['Contactgroup'][] = [
                    'id'   => $contactgroupId,
                    'name' => $contactgroupName
                ];
            }
        }

        if (!empty($dataToParse['Host']['check_period_id'])) {
            foreach ($TimeperiodsTable->getTimeperiodsAsList($dataToParse['Host']['check_period_id']) as $timeperiodId => $timeperiodName) {
                $extDataForChangelog['CheckPeriod'] = [
                    'id'   => $timeperiodId,
                    'name' => $timeperiodName
                ];
            }
        }

        if (!empty($dataToParse['Host']['notify_period_id'])) {
            foreach ($TimeperiodsTable->getTimeperiodsAsList($dataToParse['Host']['notify_period_id']) as $timeperiodId => $timeperiodName) {
                $extDataForChangelog['NotifyPeriod'] = [
                    'id'   => $timeperiodId,
                    'name' => $timeperiodName
                ];
            }
        }

        if (!empty($dataToParse['Host']['command_id'])) {
            foreach ($CommandsTable->getCommandByIdAsList($dataToParse['Host']['command_id']) as $commandId => $commandName) {
                $extDataForChangelog['CheckCommand'] = [
                    'id'   => $commandId,
                    'name' => $commandName
                ];
            }
        }

        if (!empty($dataToParse['Host']['hostgroups']['_ids'])) {
            foreach ($HostgroupsTable->getHostgroupsAsList($dataToParse['Host']['hostgroups']['_ids']) as $hostgroupId => $hostgroupName) {
                $extDataForChangelog['Hostgroup'][] = [
                    'id'   => $hostgroupId,
                    'name' => $hostgroupName
                ];
            }
        }

        if (!empty($dataToParse['Host']['parenthosts']['_ids'])) {
            foreach ($this->getHostsAsList($dataToParse['Host']['parenthosts']['_ids']) as $parentHostId => $parentHostName) {
                $extDataForChangelog['Parenthost'][] = [
                    'id'   => $parentHostId,
                    'name' => $parentHostName
                ];
            }
        }

        if (!empty($dataToParse['Host']['hosttemplate_id'])) {
            foreach ($HosttemplatesTable->getHosttemplatesAsList($dataToParse['Host']['hosttemplate_id']) as $hosttemplateId => $hosttemplateName) {
                $extDataForChangelog['Hosttemplate'][] = [
                    'id'   => $hosttemplateId,
                    'name' => $hosttemplateName
                ];
            }
        }

        return $extDataForChangelog;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getHostsAsList($ids = []) {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.name'
            ])
            ->disableHydration();
        if (!empty($ids)) {
            $query->where([
                'Hosts.id IN' => $ids
            ]);
        }

        return $this->formatListAsCake2($query->toArray());
    }

    /**
     * @param int $id
     * @return array
     */
    public function getHostForEdit($id) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'Contactgroups',
                'Contacts',
                'Hostgroups',
                'Customvariables',
                'Parenthosts',
                'HostsToContainersSharing',
                'Hostcommandargumentvalues' => [
                    'Commandarguments'
                ]
            ])
            ->disableHydration()
            ->first();

        $host = $query;
        $host['hostgroups'] = [
            '_ids' => Hash::extract($query, 'hostgroups.{n}.id')
        ];
        $host['contacts'] = [
            '_ids' => Hash::extract($query, 'contacts.{n}.id')
        ];
        $host['contactgroups'] = [
            '_ids' => Hash::extract($query, 'contactgroups.{n}.id')
        ];
        $host['parenthosts'] = [
            '_ids' => Hash::extract($query, 'parenthosts.{n}.id')
        ];
        $host['hosts_to_containers_sharing'] = [
            '_ids' => Hash::extract($query, 'hosts_to_containers_sharing.{n}.id')
        ];

        return [
            'Host' => $host
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    public function getHostForServiceEdit($id) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'HostsToContainersSharing',
            ])
            ->disableHydration()
            ->first();

        $host = $query;
        $host['hosts_to_containers_sharing'] = [
            '_ids' => Hash::extract($query, 'hosts_to_containers_sharing.{n}.id')
        ];

        return [
            'Host' => $host
        ];
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function getHostForBrowser($id) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'Contactgroups'             => [
                    'Containers'
                ],
                'Contacts'                  => [
                    'Containers'
                ],
                'Hostgroups',
                'Customvariables',
                'Parenthosts',
                'HostsToContainersSharing',
                'Hostcommandargumentvalues' => [
                    'Commandarguments'
                ]
            ])
            ->disableHydration()
            ->first();

        return $query;
    }

    /**
     * @param HostConditions $HostConditions
     * @param int|array $selected
     * @return array|null
     */
    public function getHostsForAngular(HostConditions $HostConditions, $selected = []) {
        if (!is_array($selected)) {
            $selected = [$selected];
        }

        $query = $this->find('list');
        $MY_RIGHTS = $HostConditions->getContainerIds();
        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing'
        ]);
        $where = $HostConditions->getWhereForFind();

        if (is_array($selected)) {
            $selected = array_filter($selected);
        }
        if (!empty($selected)) {
            $where['NOT'] = [
                'Hosts.id IN' => $selected
            ];
        }

        if ($HostConditions->hasNotConditions()) {
            if (!empty($where['NOT'])) {
                $where['NOT'] = array_merge($where['NOT'], $HostConditions->getNotConditions());
            } else {
                if (!empty($HostConditions->getNotConditions())) {
                    $where['NOT'] = $HostConditions->getNotConditions();
                }
            }
        }

        if (!empty($where)) {
            $query->where($where);
        }
        $query->group(['Hosts.id']);
        $query->order([
            'Hosts.name' => 'asc'
        ]);
        $query->limit(ITN_AJAX_LIMIT);

        $hostsWithLimit = $query->toArray();

        $selectedHosts = [];
        if (!empty($selected)) {
            $query = $this->find('list');
            $MY_RIGHTS = $HostConditions->getContainerIds();
            if (!empty($MY_RIGHTS)) {
                $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                    'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
                ]);
            }
            $query->contain([
                'HostsToContainersSharing'
            ]);
            $where = [
                'Hosts.id IN' => $selected
            ];
            if ($HostConditions->includeDisabled() === false) {
                $where['Hosts.disabled'] = 0;
            }
            if ($HostConditions->hasNotConditions()) {
                if (!empty($where['NOT'])) {
                    $where['NOT'] = array_merge($where['NOT'], $HostConditions->getNotConditions());
                } else {
                    $where['NOT'] = $HostConditions->getNotConditions();
                }
            }


            if (!empty($where)) {
                $query->where($where);
            }
            $query->group(['Hosts.id']);
            $query->order([
                'Hosts.name' => 'asc'
            ]);

            $selectedHosts = $query->toArray();

        }

        $hosts = $hostsWithLimit + $selectedHosts;
        asort($hosts, SORT_FLAG_CASE | SORT_NATURAL);
        return $hosts;
    }

    /**
     * @param null|int $limit
     * @param null|int $offset
     * @param null|string $uuid
     * @return array|Query
     */
    public function getHostsForExport($limit = null, $offset = null, $uuid = null) {
        $where = [
            'Hosts.disabled' => 0
        ];
        if ($uuid !== null) {
            $where['Hosts.uuid'] = $uuid;
        }


        $query = $this->find()
            ->where([
                'Hosts.disabled' => 0
            ])
            ->contain([
                'Hosttemplates'             =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id', 'uuid', 'check_interval', 'command_id']);
                    },
                'Contactgroups'             =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id', 'uuid']);
                    },
                'Contacts'                  =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id', 'uuid']);
                    },
                'Hostgroups'                =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id', 'uuid']);
                    },
                'Customvariables',
                'Parenthosts'               =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id', 'uuid', 'disabled', 'satellite_id']);
                    },
                'Hostcommandargumentvalues' => [
                    'Commandarguments'
                ]
            ]);

        if ($limit !== null) {
            $query->limit($limit);
        }
        if ($offset !== null) {
            $query->offset($offset);
        }

        $query->all();
        return $query;
    }

    /**
     * @return Query
     */
    public function getHostsForServiceExport() {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.uuid',
                'Hosts.satellite_id',
            ])
            ->where([
                'Hosts.disabled' => 0
            ])
            ->all();

        return $query;
    }

    /**
     * @return int|null
     */
    public function getHostsCountForExport() {
        $query = $this->find()
            ->where([
                'Hosts.disabled' => 0
            ])
            ->count();

        return $query;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getHostSharing($id) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.uuid',
                'Hosts.name',
                'Hosts.container_id',
                'Hosts.host_type'
            ])
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'HostsToContainersSharing',
            ])
            ->disableHydration()
            ->first();

        $host = $query;
        $host['hosts_to_containers_sharing'] = [
            '_ids' => Hash::extract($query, 'hosts_to_containers_sharing.{n}.id')
        ];

        return [
            'Host' => $host
        ];
    }

    /**
     * @param int $id
     * @return string
     */
    public function getHostUuidById($id) {
        $query = $this->find()
            ->select([
                'Hosts.uuid',
            ])
            ->where([
                'Hosts.id' => $id
            ]);

        $host = $query->firstOrFail();

        return $host->get('uuid');
    }

    /**
     * @param $uuid
     * @return int
     */
    public function getHostIdByUuid($uuid) {
        $query = $this->find()
            ->select([
                'Hosts.id',
            ])
            ->where([
                'Hosts.uuid' => $uuid
            ]);

        $host = $query->firstOrFail();

        return $host->get('id');
    }

    /**
     * @param int $hostId
     * @return int
     */
    public function getHostPrimaryContainerIdByHostId($hostId) {
        $host = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.container_id',
            ])
            ->where([
                'Hosts.id' => $hostId
            ])
            ->firstOrFail();

        return $host->get('container_id');
    }

    /**
     * @param int $hostId
     * @return array
     */
    public function getHostContainerIdsByHostId($hostId) {
        /** @var Host $host */
        $host = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.container_id'
            ])
            ->contain([
                'HostsToContainersSharing',
            ])
            ->where([
                'Hosts.id' => $hostId
            ])
            ->firstOrFail();

        return $host->getContainerIds();
    }

    /**
     * @param int $id
     * @return array|\Cake\Datasource\EntityInterface
     */
    public function getContactsAndContactgroupsById($id) {
        $query = $this->find()
            ->select([
                'Hosts.id'
            ])
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'Contactgroups',
                'Contacts'
            ])
            ->disableHydration()
            ->firstOrFail();

        $host = $query;
        $host['contacts'] = [
            '_ids' => Hash::extract($query, 'contacts.{n}.id')
        ];
        $host['contactgroups'] = [
            '_ids' => Hash::extract($query, 'contactgroups.{n}.id')
        ];

        return $host;
    }

    /**
     * @param int $id
     * @return array|\Cake\Datasource\EntityInterface
     */
    public function getContactsAndContactgroupsByIdForServiceBrowser($id) {
        $query = $this->find()
            ->select([
                'Hosts.id'
            ])
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'Contactgroups' => [
                    'Containers'
                ],
                'Contacts'      => [
                    'Containers'
                ]
            ])
            ->disableHydration()
            ->firstOrFail();

        $host = $query;

        return $host;
    }

    /**
     * @param int $hostId
     * @return array
     */
    public function getServicesForServicetemplateAllocation($hostId) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $hostId
            ])
            ->select([
                'Hosts.id',
                'Hosts.uuid',
                'Hosts.name',
                'Hosts.address'
            ])
            ->contain([
                'Services' => function (Query $query) {
                    $query->disableAutoFields()
                        ->select([
                            'Services.id',
                            'Services.name',
                            'Services.host_id',
                            'Services.servicetemplate_id',
                            'Services.disabled'
                        ])
                        ->contain([
                            'Servicetemplates' => function (Query $query) {
                                $query->disableAutoFields()
                                    ->select([
                                        'Servicetemplates.id',
                                        'Servicetemplates.name'
                                    ]);
                                return $query;
                            }
                        ]);
                    return $query;
                }
            ])
            ->disableHydration()
            ->first();

        $result = $query;
        $result['services'] = [];

        //Use servicetemplate id as array key
        foreach ($query['services'] as $service) {
            $result['services'][$service['servicetemplate_id']] = $service;
        }

        return $result;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function existsById($id) {
        return $this->exists(['Hosts.id' => $id]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function existsByIdAndType($id, $type) {
        return $this->exists([
            'Hosts.id'        => $id,
            'Hosts.host_type' => $type
        ]);
    }

    /**
     * @param int $commandId
     * @return bool
     */
    public function isCommandUsedByHost($commandId) {
        $count = $this->find()
            ->where([
                'Hosts.command_id' => $commandId,
            ])->count();

        if ($count > 0) {
            return true;
        }

        $count = $this->find()
            ->where([
                'Hosts.eventhandler_command_id' => $commandId,
            ])->count();

        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param int $commandId
     * @param array $MY_RIGHTS
     * @param bool $enableHydration
     * @return array
     */
    public function getHostsByCommandId($commandId, $MY_RIGHTS = [], $enableHydration = true) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.name',
                'Hosts.uuid'
            ]);

        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing',
        ]);

        $query->andWhere([
            'OR' => [
                ['Hosts.command_id' => $commandId],
                ['Hosts.eventhandler_command_id' => $commandId]
            ]
        ])
            ->order(['Hosts.name' => 'asc'])
            ->enableHydration($enableHydration)
            ->group(['Hosts.id'])
            ->all();

        return $this->emptyArrayIfNull($query->toArray());
    }

    /**
     * @param int $contactId
     * @param array $MY_RIGHTS
     * @param bool $enableHydration
     * @return array
     */
    public function getHostsByContactId($contactId, $MY_RIGHTS = [], $enableHydration = true) {

        /** @var ContactsToHostsTable $ContactsToHostsTable */
        $ContactsToHostsTable = TableRegistry::getTableLocator()->get('ContactsToHosts');

        $query = $ContactsToHostsTable->find()
            ->select([
                'host_id'
            ])
            ->where([
                'contact_id' => $contactId
            ])
            ->group([
                'host_id'
            ])
            ->disableHydration()
            ->all();

        $result = $query->toArray();
        if (empty($result)) {
            return [];
        }

        $hostIds = Hash::extract($result, '{n}.host_id');

        $query = $this->find('all');
        $where = [
            'Hosts.id IN' => $hostIds
        ];

        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing'
        ]);

        $query->where($where);
        $query->enableHydration($enableHydration);
        $query->order([
            'Hosts.name' => 'asc'
        ]);
        $query->group([
            'Hosts.id'
        ]);

        $result = $query->all();

        return $this->emptyArrayIfNull($result->toArray());
    }

    /**
     * @return array
     */
    public function getHostsThatUseOitcAgentForExport() {
        $query = $this->find()
            ->disableHydration()
            ->select([
                'Hosts.id',
                'Hosts.name',
                'Hosts.uuid',
                'Hosts.address'
            ])
            ->innerJoinWith('Services', function (Query $query) {
                $query->where([
                    'Services.service_type' => OITC_AGENT_SERVICE
                ]);
                return $query;
            })
            ->contain([
                'Agentconfigs'    => function (Query $query) {
                    return $query->enableAutoFields();
                },
                'Agenthostscache' => function (Query $query) {
                    return $query->enableAutoFields();
                }
            ])
            ->group([
                'Hosts.id'
            ])
            ->all();

        $rawHosts = $query->toArray();
        if ($rawHosts === null) {
            return [];
        }

        $hosts = [];
        foreach ($rawHosts as $host) {
            $hosts[$host['id']] = $host;
        }

        return $hosts;
    }

    public function hasHostServiceFromServicetemplateId($hostId, $servicetemplateId) {
        $count = $this->find()
            ->where([
                'Hosts.id' => $hostId,
            ])
            ->innerJoinWith('Services', function (Query $query) use ($servicetemplateId) {
                $query->where([
                    'Services.servicetemplate_id' => $servicetemplateId
                ]);
                return $query;
            })
            ->count();

        return $count > 0;
    }

    /**
     * @param bool $includeDisabled
     * @return int|null
     */
    public function getHostsCountForStats($includeDisabled = true) {
        $query = $this->find();
        if ($includeDisabled === false) {
            $query->where([
                'Hosts.disabled' => 0
            ]);
        }

        return $query->count();
    }

    /**
     * @param int $id
     * @return array|Host|null
     */
    public function getHostByIdForServiceBrowser($id) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.uuid',
                'Hosts.name',
                'Hosts.address',
                'Hosts.container_id',
                'Hosts.satellite_id'
            ])
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'HostsToContainersSharing',
                'Contacts',
                'Contactgroups'
            ])
            ->first();
        return $query;
    }

    /**
     * @param HostFilter $HostFilter
     * @param HostConditions $HostConditions
     * @param null|PaginateOMat $PaginateOMat
     * @param string $type (all or count, list is NOT supported!)
     * @return int|array
     */
    public function getHostsByRegularExpression(HostFilter $HostFilter, HostConditions $HostConditions, $PaginateOMat = null, $type = 'all') {
        $MY_RIGHTS = $HostConditions->getContainerIds();

        $query = $this->find('all');
        $query->select([
            'Hosts.id',
            'Hosts.uuid',
            'Hosts.name',
            'Hosts.description',
            'Hosts.address',
            'Hosts.satellite_id',
            'Hosts.container_id'
        ]);

        $where = [
            'Hosts.disabled'    => (int)$HostConditions->includeDisabled(),
            'Hosts.name REGEXP' => $HostConditions->getHostnameRegex()
        ];

        $query->where($where);
        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing',
            'Hosttemplates' => [
                'fields' => [
                    'Hosttemplates.id',
                    'Hosttemplates.name'
                ]
            ]

        ]);
        $query->disableHydration();
        $query->group(['Hosts.id']);
        if ($type === 'all') {
            $query->order($HostFilter->getOrderForPaginator('Hosts.name', 'asc'));
        }

        if ($type === 'count') {
            $count = $query->count();
            return $count;
        }

        if ($PaginateOMat === null) {
            //Just execute query
            $result = $this->formatResultAsCake2($query->toArray(), false);
        } else {
            if ($PaginateOMat->useScroll()) {
                $result = $this->scroll($query, $PaginateOMat->getHandler(), false);
            } else {
                $result = $this->paginate($query, $PaginateOMat->getHandler(), false);
            }
        }
        return $result;
    }

    /**
     * @param array $MY_RIGHTS
     * @param bool $includeOkState
     * @return array
     */
    public function getHoststatusCount($MY_RIGHTS, $includeOkState = false) {
        $hoststatusCount = [
            '1' => 0,
            '2' => 0,
        ];
        if ($includeOkState === true) {
            $hoststatusCount['0'] = 0;
        }

        $query = $this->find();
        $query
            ->select([
                'Hoststatus.current_state',
                'Hosts.id',
                'count' => $query->newExpr('COUNT(DISTINCT Hoststatus.host_object_id)'),
            ])
            ->where([
                'Hosts.disabled'       => 0,
                'HostObject.is_active' => 1,
            ])
            ->join([
                'a' => [
                    'table'      => 'nagios_objects',
                    'type'       => 'INNER',
                    'alias'      => 'HostObject',
                    'conditions' => 'Hosts.uuid = HostObject.name1 AND HostObject.objecttype_id = 1'
                ],
                'b' => [
                    'table'      => 'nagios_hoststatus',
                    'type'       => 'INNER',
                    'alias'      => 'Hoststatus',
                    'conditions' => 'Hoststatus.host_object_id = HostObject.object_id',
                ]
            ]);

        if (!empty($MY_RIGHTS)) {
            $query
                ->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                    'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
                ]);
        }

        $query
            ->contain([
                'HostsToContainersSharing'
            ])
            ->group([
                'Hoststatus.current_state',
            ])
            ->disableHydration();

        if ($includeOkState === false) {
            $query->andWhere([
                'Hoststatus.current_state >' => 0
            ]);
        }


        $hoststatusCountResult = $query->all();

        foreach ($hoststatusCountResult as $hoststatus) {
            $hoststatusCount[$hoststatus['Hoststatus']['current_state']] = (int)$hoststatus['count'];
        }
        return $hoststatusCount;
    }

    /**
     * @param array $MY_RIGHTS
     * @param bool $includeOkState
     * @return array
     */
    public function getServicestatusCount($MY_RIGHTS, $includeOkState = false) {
        $servicestatusCount = [
            '1' => 0,
            '2' => 0,
            '3' => 0,
        ];
        if ($includeOkState === true) {
            $servicestatusCount['0'] = 0;
        }

        $query = $this->find();
        $query
            ->select([
                'Servicestatus.current_state',
                'Hosts.id',
                'count' => $query->newExpr('COUNT(DISTINCT Servicestatus.service_object_id)'),
            ])
            ->where([
                'Services.disabled'       => 0,
                'ServiceObject.is_active' => 1,
            ])
            ->join([
                'a' => [
                    'table'      => 'services',
                    'type'       => 'INNER',
                    'alias'      => 'Services',
                    'conditions' => 'Services.host_id = Hosts.id',
                ],
                'b' => [
                    'table'      => 'nagios_objects',
                    'type'       => 'INNER',
                    'alias'      => 'ServiceObject',
                    'conditions' => 'ServiceObject.name2 = Services.uuid',
                ],
                'c' => [
                    'table'      => 'nagios_servicestatus',
                    'type'       => 'INNER',
                    'alias'      => 'Servicestatus',
                    'conditions' => 'Servicestatus.service_object_id = ServiceObject.object_id',
                ],
            ]);
        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing'
        ])
            ->group([
                'Servicestatus.current_state',
            ])
            ->disableHydration();

        if ($includeOkState === false) {
            $query->andWhere([
                'Servicestatus.current_state >' => 0
            ]);
        }

        $servicestatusCountResult = $query->all();

        foreach ($servicestatusCountResult as $servicestatus) {
            $servicestatusCount[$servicestatus['Servicestatus']['current_state']] = (int)$servicestatus['count'];
        }
        return $servicestatusCount;
    }

    /**
     * @param array $MY_RIGHTS
     * @param array $conditions
     * @return int
     */
    public function getHoststatusCountBySelectedStatus($MY_RIGHTS, $conditions) {

        $query = $this->find();
        $query
            ->select([
                'count' => $query->newExpr('COUNT(DISTINCT Hoststatus.host_object_id)'),
                'Hosts.id'
            ])
            ->where([
                'HostObject.is_active' => 1,
                'Hosts.disabled'       => 0
            ])
            ->join([
                'a' => [
                    'table'      => 'nagios_objects',
                    'type'       => 'INNER',
                    'alias'      => 'HostObject',
                    'conditions' => 'Hosts.uuid = HostObject.name1 AND HostObject.objecttype_id = 1'
                ],
                'b' => [
                    'table'      => 'nagios_hoststatus',
                    'type'       => 'INNER',
                    'alias'      => 'Hoststatus',
                    'conditions' => 'Hoststatus.host_object_id = HostObject.object_id',
                ]
            ]);
        if (!empty($MY_RIGHTS)) {
            $query->innerJoin(['HostsToContainersSharing' => 'hosts_to_containers'], [
                'HostsToContainersSharing.container_id IN' => $MY_RIGHTS
            ]);
        }
        $query->contain([
            'HostsToContainersSharing'
        ])
            ->disableHydration();

        $where = [];
        if (!empty($conditions['Host']['name'])) {
            $where['Hosts.name LIKE'] = sprintf('%%%s%%', $conditions['Host']['name']);
        }

        $where['Hoststatus.current_state'] = $conditions['Hoststatus']['current_state'];

        if ($where['Hoststatus.current_state'] > 0) {
            if ($conditions['Hoststatus']['problem_has_been_acknowledged'] === false) {
                $where['Hoststatus.problem_has_been_acknowledged'] = false;
            }
            if ($conditions['Hoststatus']['scheduled_downtime_depth'] === false) {
                $where['Hoststatus.scheduled_downtime_depth'] = false;
            }
        }

        $query->andWhere($where);
        $result = $query->first();

        if ($result === null) {
            return 0;
        }

        return $result['count'];
    }

    /**
     * @param int $timeperiodId
     * @return bool
     */
    public function isTimeperiodUsedByHost($timeperiodId) {
        $count = $this->find()
            ->where([
                'OR' => [
                    'Hosts.check_period_id'  => $timeperiodId,
                    'Hosts.notify_period_id' => $timeperiodId
                ]
            ])->count();

        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getHostsForCopy($ids = []) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.name',
                'Hosts.description',
                'Hosts.address',
                'Hosts.host_url'
            ])
            ->where(['Hosts.id IN' => $ids])
            ->order(['Hosts.id' => 'asc'])
            ->disableHydration()
            ->all();

        return $this->formatResultAsCake2($query->toArray(), false);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getHostsForEditDetails($ids = []) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.hosttemplate_id',
                'Hosts.container_id',
                'Hosts.description',
                'Hosts.host_url',
                'Hosts.tags',
                'Hosts.check_interval',
                'Hosts.retry_interval',
                'Hosts.max_check_attempts',
                'Hosts.notification_interval',
                'Hosts.notes',
                'Hosts.priority'
            ])
            ->contain([
                'HostsToContainersSharing' => [
                    'fields' => [
                        'HostsToContainers.host_id',
                        'HostsToContainers.container_id'
                    ]
                ],
                'Contacts'                 => [
                    'fields' => [
                        'ContactsToHosts.host_id',
                        'Contacts.id'
                    ]
                ],
                'Contactgroups'            => [
                    'fields' => [
                        'ContactgroupsToHosts.host_id',
                        'Contactgroups.id'
                    ]
                ],
                'Hosttemplates'            => [
                    'fields'        => [
                        'Hosttemplates.id',
                        'Hosttemplates.description',
                        'Hosttemplates.host_url',
                        'Hosttemplates.tags',
                        'Hosttemplates.check_interval',
                        'Hosttemplates.retry_interval',
                        'Hosttemplates.max_check_attempts',
                        'Hosttemplates.notification_interval',
                        'Hosttemplates.notes',
                        'Hosttemplates.priority'
                    ],
                    'Contacts'      => [
                        'fields' => [
                            'ContactsToHosttemplates.hosttemplate_id',
                            'Contacts.id'
                        ]
                    ],
                    'Contactgroups' => [
                        'fields' => [
                            'ContactgroupsToHosttemplates.hosttemplate_id',
                            'Contactgroups.id'
                        ]
                    ],
                ]
            ])
            ->where(['Hosts.id IN' => $ids])
            ->order(['Hosts.id' => 'asc'])
            ->disableHydration()
            ->all();

        $result = $query->toArray();
        if (empty($result)) {
            return [];
        }
        return $result;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getHostDetailsForCopy($id) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'HostsToContainersSharing'  =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id']);
                    },
                'Contactgroups'             =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id']);
                    },
                'Contacts'                  =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id']);
                    },
                'Hostgroups'                =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select(['id']);
                    },
                'Customvariables',
                'Parenthosts'               =>
                    function (Query $q) {
                        return $q->enableAutoFields(false)->select([
                            'id',
                            'name'
                        ]);
                    },
                'Hostcommandargumentvalues' => [
                    'Commandarguments'
                ],
                'CheckPeriod',
                'NotifyPeriod'
            ])
            ->firstOrFail();
        return $query;
    }

    /**
     * @param array $host
     * @param array $hosttemplate
     * @return array
     */
    public function getDataForChangelogCopy($host, $hosttemplate) {
        $fieldsToCheck = [
            'check_interval',
            'retry_interval',
            'max_check_attempts',
            'notification_interval',
            'notify_on_up',
            'notify_on_down',
            'notify_on_unreachable',
            'flap_detection_notifications_enabled',
            'flap_detection_on_up',
            'flap_detection_on_down',
            'flap_detection_on_unreachable',
            'notes',
            'priority',
            'tags',
            'active_checks_enabled'
        ];
        $hostcommandargumentvalue = [];
        if (!empty($host['hostcommandargumentvalues'])) {
            $hostcommandargumentvalue = $host['hostcommandargumentvalues'];
        } else {
            if (isset($host['command_id']) && ($host['command_id'] === $hosttemplate['Hosttemplate']['command_id'] || $host['command_id'] === null)) {
                $hostcommandargumentvalue = $hosttemplate['Hosttemplatecommandargumentvalue'];
            }
        }

        foreach ($hosttemplate['Hosttemplate'] as $fieldName => $fieldValue) {
            if (!in_array($fieldName, $fieldsToCheck)) {
                continue;
            }
            $dataForChangelog['Host'][$fieldName] = $fieldValue;
            if (isset($host[$fieldName]) && !empty($host[$fieldName])) {
                $dataForChangelog['Host'][$fieldName] = $host[$fieldName];

            }
            //$dataForChangelog['Host'][$fieldName] = (!empty($host[$fieldName]))?$host[$fieldName]:$hosttemplate[$fieldName];
        }
        $dataForChangelog = [
            'Host'                     => Hash::merge($dataForChangelog['Host'], $hosttemplate['Hosttemplate']),
            'Contact'                  => (!empty($host['contacts'])) ? $host['contacts'] : $hosttemplate['contacts'],
            'Contactgroup'             => (!empty($host['contactgroups'])) ? $host['contactgroups'] : $hosttemplate['contactgroups'],
            'Customvariable'           => (!empty($host['customvariables'])) ? $host['customvariables'] : $hosttemplate['customvariables'],
            'Hostcommandargumentvalue' => $hostcommandargumentvalue,
            'Hosttemplate'             => $hosttemplate['Hosttemplate'],
            'Hostgroup'                => (!empty($host['hostgroups'])) ? $host['hostgroups'] : $hosttemplate['hostgroups'],
            'Parenthost'               => $host['parenthosts'],
            'CheckPeriod'              => (empty($host['CheckPeriod'])) ? $hosttemplate['CheckPeriod'] : $host['CheckPeriod'],
            'NotifyPeriod'             => (empty($host['NotifyPeriod'])) ? $hosttemplate['NotifyPeriod'] : $host['NotifyPeriod'],
            'CheckCommand'             => (empty($host['CheckCommand'])) ? $hosttemplate['CheckCommand'] : $host['CheckCommand'],
        ];
        return $dataForChangelog;
    }

    /**
     * @param $hoststatus
     * @param bool $extended show details ('acknowledged', 'in downtime', ...)
     * @return array
     */
    public function getHostStateSummary($hoststatus, $extended = true) {
        $hostStateSummary = [
            'state' => [
                0 => 0,
                1 => 0,
                2 => 0
            ],
            'total' => 0
        ];
        if ($extended === true) {
            $hostStateSummary = [
                'state'        => [
                    0 => 0,
                    1 => 0,
                    2 => 0
                ],
                'acknowledged' => [
                    0 => 0,
                    1 => 0,
                    2 => 0
                ],
                'in_downtime'  => [
                    0 => 0,
                    1 => 0,
                    2 => 0
                ],
                'not_handled'  => [
                    0 => 0,
                    1 => 0,
                    2 => 0
                ],
                'passive'      => [
                    0 => 0,
                    1 => 0,
                    2 => 0
                ],
                'total'        => 0
            ];
        }
        if (empty($hoststatus)) {
            return $hostStateSummary;
        }
        foreach ($hoststatus as $host) {
            //Check for randome exit codes like 255...
            if ($host['Hoststatus']['current_state'] > 2) {
                $host['Hoststatus']['current_state'] = 2;
            }

            $hostStateSummary['state'][$host['Hoststatus']['current_state']]++;
            if ($extended === true) {
                if ($host['Hoststatus']['current_state'] > 0) {
                    if ($host['Hoststatus']['problem_has_been_acknowledged'] > 0) {
                        $hostStateSummary['acknowledged'][$host['Hoststatus']['current_state']]++;
                    } else {
                        $hostStateSummary['not_handled'][$host['Hoststatus']['current_state']]++;
                    }
                }

                if ($host['Hoststatus']['scheduled_downtime_depth'] > 0) {
                    $hostStateSummary['in_downtime'][$host['Hoststatus']['current_state']]++;
                }
                if ($host['Hoststatus']['active_checks_enabled'] == 0) {
                    $hostStateSummary['passive'][$host['Hoststatus']['current_state']]++;
                }
            }
            $hostStateSummary['total']++;
        }
        return $hostStateSummary;
    }

    /**
     * @return array
     */
    public function parentHostsWithChildIds() {
        $query = $this->find()
            ->join([
                [
                    'table'      => 'hosts_to_parenthosts',
                    'alias'      => 'Parenthosts',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Hosts.id = Parenthosts.parenthost_id',
                    ],
                ],
                [
                    'table'      => 'hosts',
                    'alias'      => 'HostToParenthostParent',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Parenthosts.parenthost_id = HostToParenthostParent.id',
                    ],
                ],
                [
                    'table'      => 'hosts',
                    'alias'      => 'HostToParenthostChild',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Parenthosts.host_id = HostToParenthostChild.id',
                    ],
                ]
            ])
            ->select([
                'HostToParenthostChild.id',
                'HostToParenthostParent.id'
            ])
            ->disableHydration()
            ->all();

        if (empty($query)) {
            return [];
        }
        return $query->toArray();
    }

    public function getHostsForStatusmaps($conditions = [], $containerIds = [], $allHostIds = [], $count = false, $limit = null, $offset = null) {
        $query = $this->find()
            ->contain([
                'Parenthosts' => function (Query $q) {
                    return $q->select([
                        'Parenthosts.id'
                    ]);
                }
            ])
            ->select([
                'Hosts.id',
                'Hosts.uuid',
                'Hosts.name',
                'Hosts.description',
                'Hosts.address',
                'Hosts.disabled',
                'Hosts.satellite_id',
            ]);

        if (!empty($containerIds)) {
            $query->contain([
                'HostsToContainersSharing' => function (Query $query) use ($containerIds) {
                    return $query->where([
                        'HostsToContainersSharing.id IN' => $containerIds
                    ]);
                }
            ])->group([
                'Hosts.id'
            ]);
        }
        if (!empty($allHostIds)) {
            $query->where([
                'Hosts.id IN' => $allHostIds
            ]);
        }
        if (!empty($conditions)) {
            $query->where($conditions);
        }
        if ($limit !== null) {
            $query->limit($limit);
        }
        if ($offset !== null) {
            $query->offset($offset);
        }
        $query->disableHydration()->all();

        if ($count === true) {
            return $query->count();
        }

        if (empty($query)) {
            return [];
        }
        return $query->toArray();
    }


    /**
     * @param int $id
     * @param bool $enableHydration
     * @return array|\Cake\Datasource\EntityInterface
     */
    public function getHostsbyIdWithDetails($id, $enableHydration = true) {
        $query = $this->find()
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'HostsToContainersSharing',
                'Hosttemplates'
            ])
            ->enableHydration($enableHydration);

        return $query->firstOrFail();
    }

    /**
     * @param int $timeperiodId
     * @param array $MY_RIGHTS
     * @param bool $enableHydration
     * @return array
     */
    public function getHostsByTimeperiodId($timeperiodId, $MY_RIGHTS = [], $enableHydration = true) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.name'
            ])
            ->where([
                'OR' => [
                    'Hosts.check_period_id'  => $timeperiodId,
                    'Hosts.notify_period_id' => $timeperiodId
                ]
            ]);

        if (!empty($MY_RIGHTS)) {
            $query->contain([
                'HostsToContainersSharing'
            ])->where([
                'HostsToContainers.container_id IN' => $MY_RIGHTS
            ])->group([
                'Host.id'
            ]);
        }

        $query->enableHydration($enableHydration);

        $result = $query->all();
        return $this->emptyArrayIfNull($result->toArray());
    }

    /**
     * @param int $id
     * @param int $USAGE_FLAG
     * @return Host|bool
     */
    public function setUsageFlagById($id, int $USAGE_FLAG) {
        $host = $this->get($id);
        $currentFlag = $host->get('usage_flag');

        if ($currentFlag & $USAGE_FLAG) {
            //Host already has the flag for given module
            return true;
        } else {
            $newFlag = $currentFlag + $USAGE_FLAG;
            $host->set('usage_flag', $newFlag);
            return $this->save($host);
        }
    }

    /**
     * @param int $id
     * @param int $USAGE_FLAG
     * @return Host|bool
     */
    public function removeUsageFlagById($id, int $USAGE_FLAG) {
        $host = $this->get($id);
        $currentFlag = $host->get('usage_flag');

        if ($currentFlag & $USAGE_FLAG) {
            $newFlag = $currentFlag - $USAGE_FLAG;
            if ($newFlag < 0) {
                $newFlag = 0;
            }

            $host->set('usage_flag', $newFlag);
            return $this->save($host);
        }

        return true;
    }

    /**
     * @param Host $Host
     * @param User $User
     * @return bool
     */
    public function __delete(Host $Host, User $User) {
        $hostdependencies = $Host->get('hostdependencies_host_memberships');
        $hostdependenciesToDelete = [];

        if (!empty($hostdependencies)) {
            /** @var HostdependenciesTable $HostdependenciesTable */
            $HostdependenciesTable = TableRegistry::getTableLocator()->get('Hostdependencies');
            /** @var Hostdependency $Hostdependency */
            foreach ($hostdependencies as $Hostdependency) {
                $hostdependencyId = $Hostdependency->get('hostdependency_id');
                $hostdependencyIsBroken = $HostdependenciesTable->isHostdependencyBroken(
                    $hostdependencyId,
                    $Host->get('id')
                );
                if ($hostdependencyIsBroken === true) {
                    $hostdependenciesToDelete[] = $Hostdependency;
                }
            }
        }

        $hostescalations = $Host->get('hostescalations_host_memberships');
        $hostescalationsToDelete = [];
        if (!empty($hostescalations)) {
            /** @var HostescalationsTable $HostescalationsTable */
            $HostescalationsTable = TableRegistry::getTableLocator()->get('Hostescalations');
            /** @var Hostescalation $Hostescalation */
            foreach ($hostescalations as $Hostescalation) {
                $hostescalationId = $Hostescalation->get('hostescalation_id');
                $hostescalationIsBroken = $HostescalationsTable->isHostescalationBroken(
                    $hostescalationId,
                    $Host->get('id')
                );
                if ($hostescalationIsBroken === true) {
                    $hostescalationsToDelete[] = $Hostescalation;
                }
            }
        }

        if (!$this->delete($Host)) {
            return false;
        }

        /** @var DocumentationsTable $DocumentationsTable */
        $DocumentationsTable = TableRegistry::getTableLocator()->get('Documentations');
        /** @var DeletedHostsTable $DeletedHostsTable */
        $DeletedHostsTable = TableRegistry::getTableLocator()->get('DeletedHosts');
        /** @var ChangelogsTable $ChangelogsTable */
        $ChangelogsTable = TableRegistry::getTableLocator()->get('Changelogs');

        $changelog_data = $ChangelogsTable->parseDataForChangelog(
            'delete',
            'hosts',
            $Host->get('id'),
            OBJECT_HOST,
            $Host->get('container_id'),
            $User->getId(),
            $Host->get('name'),
            [
                'Host' => $Host->toArray()
            ]
        );

        if ($changelog_data) {
            /** @var Changelog $changelogEntry */
            $changelogEntry = $ChangelogsTable->newEntity($changelog_data);
            $ChangelogsTable->save($changelogEntry);
        }

        if ($DocumentationsTable->existsByUuid($Host->get('uuid'))) {
            $DocumentationsTable->delete($DocumentationsTable->getDocumentationByUuid($Host->get('uuid')));
        }

        $this->_clenupHostEscalationAndDependency($hostdependenciesToDelete, $hostescalationsToDelete);

        //Save host to DeletedHostsTable
        $data = $DeletedHostsTable->newEntity([
            'host_id'          => $Host->get('id'),
            'uuid'             => $Host->get('uuid'),
            'hosttemplate_id'  => $Host->get('hosttemplate_id'),
            'name'             => $Host->get('name'),
            'description'      => $Host->get('description'),
            'deleted_perfdata' => 0,
        ]);
        $DeletedHostsTable->save($data);

        return true;
    }

    /**
     * Check if the host was part of an hostescalation or hostdependency
     * If yes, cake delete the records by it self, but may be we have an empty hostescalation or hostgroup now.
     * Nagios don't relay like this so we need to check this and delete the host escalation or host dependency if empty
     *
     * @param array $hostdependenciesMembershipToDelete
     * @param array $hostescalationsMembershipToDelete
     */
    public function _clenupHostEscalationAndDependency($hostdependenciesMembershipToDelete = [], $hostscalationsMembershipToDelete = []) {
        if (!empty($hostdependenciesMembershipToDelete)) {
            /** @var HostdependenciesTable $HostdependenciesTable */
            $HostdependenciesTable = TableRegistry::getTableLocator()->get('Hostdependencies');
            foreach ($hostdependenciesMembershipToDelete as $hostdependencyMembership) {
                if ($HostdependenciesTable->existsById($hostdependencyMembership->get('hostdependency_id'))) {
                    $hostdependency = $HostdependenciesTable->get($hostdependencyMembership->get('hostdependency_id'));
                    $HostdependenciesTable->delete($hostdependency);
                }
            }
        }

        if (!empty($hostscalationsMembershipToDelete)) {
            /* @var HostescalationsTable $HostescalationsTable */
            $HostescalationsTable = TableRegistry::getTableLocator()->get('Hostescalations');
            foreach ($hostscalationsMembershipToDelete as $hostescalationMembership) {
                if ($HostescalationsTable->existsById($hostescalationMembership->get('hostescalation_id'))) {
                    $hostescalation = $HostescalationsTable->get($hostescalationMembership->get('hostescalation_id'));
                    $HostescalationsTable->delete($hostescalation);
                }
            }
        }
    }

    /**
     * @param $id
     * @return array|Host|null
     */
    public function getHostByIdForTimeline($id) {
        $query = $this->find()
            ->select([
                'Hosts.id',
                'Hosts.name',
                'Hosts.uuid',
                'Hosts.container_id',
                'Hosts.hosttemplate_id',
                'Hosts.check_period_id',
                'Hosts.notify_period_id',
            ])
            ->where([
                'Hosts.id' => $id
            ])
            ->contain([
                'HostsToContainersSharing',
                'Hosttemplates' => function (Query $query) {
                    $query->select([
                        'Hosttemplates.id',
                        'Hosttemplates.check_period_id',
                        'Hosttemplates.notify_period_id'
                    ]);
                    return $query;
                }
            ])
            ->first();

        return $query;
    }
}