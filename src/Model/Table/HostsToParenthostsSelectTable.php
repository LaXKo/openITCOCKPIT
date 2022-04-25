<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * HostsToParenthosts Model
 *
 * @property \App\Model\Table\HostsTable&\Cake\ORM\Association\BelongsTo $Hosts
 * @property \App\Model\Table\ParenthostsTable&\Cake\ORM\Association\BelongsTo $Parenthosts
 *
 * @method \App\Model\Entity\HostsToParenthostSelect.php newEmptyEntity()
 * @method \App\Model\Entity\HostsToParenthostSelect newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect get($primaryKey, $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\HostsToParenthostSelect[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class HostsToParenthostsSelectTable extends Table {
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('hosts_to_parenthosts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Hosts', [
            'foreignKey' => 'host_id',
            'joinType'   => 'INNER',
        ]);
        $this->belongsTo('Parenthosts', [
            'foreignKey' => 'parenthost_id',
            'joinType'   => 'INNER',
            'className' => 'Hosts'
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
        $rules->add($rules->existsIn(['host_id'], 'Hosts'), ['errorField' => 'host_id']);
        $rules->add($rules->existsIn(['parenthost_id'], 'Parenthosts'), ['errorField' => 'parenthost_id']);

        return $rules;
    }
}
