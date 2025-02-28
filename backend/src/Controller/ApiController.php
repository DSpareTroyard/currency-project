<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

class ApiController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->viewBuilder()->setClassName('Json');
    }

    public function index()
    {
        $this->set('message', 'Currency API v1.0');
        $this->viewBuilder()->setOption('serialize', ['message']);
    }
    
    public function optionsHandler()
    {
        $this->autoRender = false;
        $response = $this->response
            ->withStatus(204)
            ->withType('application/json');
        
        return $response;
    }
};
