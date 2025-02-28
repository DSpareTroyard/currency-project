<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;

class CurrenciesController extends AppController
{
    public function index()
    {
        $this->viewBuilder()->setOption('serialize', ['currencies']);
        $this->set('currencies', $this->paginate());
    }
}