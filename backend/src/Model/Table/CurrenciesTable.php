<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

class CurrenciesTable extends Table
{
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');

        $this->setTable('currencies');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Rates', [
            'foreignKey' => 'currency_id',
            'dependent' => true
        ]);
    }
    public function findWithLatestRates(Query $query)
    {
        return $query
            ->contain([
                'Rates' => function ($q) {
                    return $q
                        ->select([
                            'currency_id',
                            'rate' => 'ROUND(original_value / nominal, 4)',
                            'nominal',
                            'original_value',
                            'date',
                            'created'
                        ])
                        ->order(['created' => 'DESC']);
                }
            ]);
    }
}