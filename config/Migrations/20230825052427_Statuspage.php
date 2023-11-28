<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Statuspage extends AbstractMigration
{
    public $autoId = false;
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        if (!$this->hasTable('statuspages')) {
            $this->table('statuspages')
                ->addColumn('id', 'integer', [
                    'autoIncrement' => true,
                    'default'       => null,
                    'limit'         => 11,
                    'null'          => false,
                ])
                ->addPrimaryKey(['id'])
                ->addColumn('name', 'string', [
                    'default' => null,
                    'limit'   => 255,
                    'null'    => false,
                ])
                ->addColumn('description', 'string', [
                    'default' => null,
                    'limit'   => 1000,
                    'null'    => true,
                ])
                ->addColumn('public', 'boolean', [
                    'default' => '0',
                    'limit'   => null,
                    'null'    => false,
                ])
                ->addColumn('show_comments', 'boolean', [
                    'default' => '0',
                    'limit'   => null,
                    'null'    => false,
                ])
                ->addColumn('created', 'datetime', [
                    'default' => null,
                    'limit'   => null,
                    'null'    => false,
                ])
                ->addColumn('modified', 'datetime', [
                    'default' => null,
                    'limit'   => null,
                    'null'    => false,
                ])
                ->create();
        }

        if (!$this->hasTable('statuspages_to_containers')) {
            $this->table('statuspages_to_containers')
                ->addColumn('id', 'integer', [
                    'autoIncrement' => true,
                    'default'       => null,
                    'limit'         => 11,
                    'null'          => false,
                ])
                ->addPrimaryKey(['id'])
                ->addColumn('container_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('statuspage_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addIndex(
                    [
                        'container_id',
                    ]
                )
                ->addIndex(
                    [
                        'statuspage_id',
                    ]
                )
                ->create();
        }

        if (!$this->hasTable('statuspages_to_hosts')) {
            $this->table('statuspages_to_hosts')
                ->addColumn('id', 'integer', [
                    'autoIncrement' => true,
                    'default'       => null,
                    'limit'         => 11,
                    'null'          => false,
                ])
                ->addPrimaryKey(['id'])
                ->addColumn('statuspage_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('host_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('display_alias', 'string', [
                    'default' => null,
                    'limit'   => 255,
                    'null'    => true,
                ])
                ->addIndex(
                    [
                        'statuspage_id',
                    ]
                )
                ->addIndex(
                    [
                        'host_id',
                    ]
                )
                ->create();
        }

        if (!$this->hasTable('statuspages_to_services')) {
            $this->table('statuspages_to_services')
                ->addColumn('id', 'integer', [
                    'autoIncrement' => true,
                    'default'       => null,
                    'limit'         => 11,
                    'null'          => false,
                ])
                ->addPrimaryKey(['id'])
                ->addColumn('statuspage_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('service_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('display_alias', 'string', [
                    'default' => null,
                    'limit'   => 255,
                    'null'    => true,
                ])
                ->addIndex(
                    [
                        'statuspage_id',
                    ]
                )
                ->addIndex(
                    [
                        'service_id',
                    ]
                )
                ->create();
        }

        if (!$this->hasTable('statuspages_to_hostgroups')) {
            $this->table('statuspages_to_hostgroups')
                ->addColumn('id', 'integer', [
                    'autoIncrement' => true,
                    'default'       => null,
                    'limit'         => 11,
                    'null'          => false,
                ])
                ->addPrimaryKey(['id'])
                ->addColumn('statuspage_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('hostgroup_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('display_alias', 'string', [
                    'default' => null,
                    'limit'   => 255,
                    'null'    => true,
                ])
                ->addIndex(
                    [
                        'statuspage_id',
                    ]
                )
                ->addIndex(
                    [
                        'hostgroup_id',
                    ]
                )
                ->create();
        }

        if (!$this->hasTable('statuspages_to_servicegroups')) {
            $this->table('statuspages_to_servicegroups')
                ->addColumn('id', 'integer', [
                    'autoIncrement' => true,
                    'default'       => null,
                    'limit'         => 11,
                    'null'          => false,
                ])
                ->addPrimaryKey(['id'])
                ->addColumn('statuspage_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('servicegroup_id', 'integer', [
                    'default' => null,
                    'limit'   => 11,
                    'null'    => false,
                ])
                ->addColumn('display_alias', 'string', [
                    'default' => null,
                    'limit'   => 255,
                    'null'    => true,
                ])
                ->addIndex(
                    [
                        'statuspage_id',
                    ]
                )
                ->addIndex(
                    [
                        'servicegroup_id',
                    ]
                )
                ->create();
        }

    }

    public function down(): void {
        $this->table('statuspages')->drop()->save();
        $this->table('statuspages_to_containers')->drop()->save();
        $this->table('statuspages_to_hosts')->drop()->save();
        $this->table('statuspages_to_servicess')->drop()->save();
        $this->table('statuspages_to_hostgroups')->drop()->save();
        $this->table('statuspages_to_servicegroups')->drop()->save();
    }
}
