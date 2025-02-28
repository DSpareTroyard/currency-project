<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateRates extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('rates');
        $table->addColumn('currency_id', 'integer', ['null' => false])
            ->addColumn('rate', 'decimal', [
                'precision' => 10,
                'scale' => 4,
                'null' => false,
                'default' => 0
            ])
            ->addColumn('date', 'date', ['null' => false])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('currency_id', 'currencies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
