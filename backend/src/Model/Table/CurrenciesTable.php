<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class CurrenciesTable extends Table
{
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');

        $this->setTable('currencies');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Rates', [
            'foreignKey' => 'currency_id'
        ]);
    }
}