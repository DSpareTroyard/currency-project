<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;

class RatesController extends AppController
{
    public function index()
    {
        $this->viewBuilder()->setOption('serialize', ['currencies']);
        $this->set('currencies', $this->paginate());
    }
}