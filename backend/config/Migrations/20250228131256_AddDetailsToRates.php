<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddDetailsToRates extends AbstractMigration
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
        $table
            ->addColumn('nominal', 'integer', [
                'default' => 1,
                'null' => false
            ])
            ->addColumn('original_value', 'decimal', [
                'precision' => 10,
                'scale' => 4,
                'null' => false
            ])
            ->update();
    }
}
