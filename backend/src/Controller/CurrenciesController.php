<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class CurrenciesController extends AppController
{
    public function index()
    {
        $currencies = Cache::remember('currency_rates_with_changes', function () {
            return $this->Currencies
                ->find('withLatestRates')
                ->all()
                ->map(function ($currency) {
                    if (count($currency->rates) > 1) {
                        $current = $currency->rates[0]->rate;
                        $previous = $currency->rates[1]->rate;
                        
                        $currency->change = match (true) {
                            $current > $previous => 'up',
                            $current < $previous => 'down',
                            default => 'equal'
                        };
                    }
                    else {
                        $currency->change = 'equal';
                    }       
                    $currency->rate = $currency->rates[0]->original_value;
                    $currency->nominal = $currency->rates[0]->nominal;
                    return $currency->toArray();
                })
                ->toArray();
        }, 'redis');


        $this->set(compact('currencies'));
        $this->viewBuilder()->setOption('serialize', ['currencies']);
    }

    public function list()
    {
        $currencies = $this->Currencies->find()
            ->select(['id', 'code', 'name'])
            ->all()
            ->toArray();

        $this->set([
            'currencies' => $currencies,
            '_serialize' => ['currencies']
        ]);
    }
}