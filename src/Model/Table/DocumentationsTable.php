<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Documentations Model
 *
 * @method \App\Model\Entity\Documentation get($primaryKey, $options = [])
 * @method \App\Model\Entity\Documentation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Documentation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Documentation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Documentation|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Documentation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Documentation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Documentation findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DocumentationsTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('documentations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('uuid')
            ->maxLength('uuid', 37)
            ->requirePresence('uuid', 'create')
            ->allowEmptyString('uuid', null, false)
            ->add('uuid', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('content')
            ->allowEmptyString('content', null, true);

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
     * @return bool
     */
    public function existsById($id) {
        return $this->exists(['Documentations.id' => $id]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function existsByUuid($uuid) {
        return $this->exists(['Documentations.uuid' => $uuid]);
    }

    /**
     * @param $uuid
     * @return array|\Cake\Datasource\EntityInterface|null
     */
    public function getDocumentationByUuid($uuid) {
        return $this->find()
            ->where(['Documentations.uuid' => $uuid])
            ->first();
    }

    /**
     * @param string $uuid
     * @return \Cake\Database\StatementInterface
     */
    public function deleteDocumentationByUuid($uuid) {
        return $this->deleteQuery()
            ->where(['Documentations.uuid' => $uuid])
            ->execute();
    }
}
