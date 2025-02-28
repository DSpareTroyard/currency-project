<?php
declare(strict_types=1);

namespace App\Command;

use Cake\I18n\Date;

use Cake\Console\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class CurrencyFetchCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $xml = simplexml_load_file('https://www.cbr.ru/scripts/XML_daily.asp');
        
        $currenciesTable = TableRegistry::getTableLocator()->get('Currencies');
        $ratesTable = TableRegistry::getTableLocator()->get('Rates');
        
        foreach ($xml->Valute as $valute) {
            $code = (string)$valute->CharCode;
            $currentValue = (float)str_replace(',', '.', (string)$valute->Value);
            $nominal = (float)$valute->Nominal;
            $currentRate = $currentValue / $nominal;

            $currency = $currenciesTable->findOrCreate(
                ['code' => $code],
                function ($entity) use ($valute) {
                    $entity->name = (string)$valute->Name;
                }
            );            

            $lastRate = $ratesTable->find()
            ->where(['currency_id' => $currency->id])
            ->order(['created' => 'DESC'])
            ->first();

            if (!$lastRate || abs($lastRate->original_value - $currentValue) > 0.01) {
                $rateData = [
                    'currency_id' => $currency->id,
                    'original_value' => $currentValue,
                    'nominal' => $nominal,
                    'rate' => $currentRate,
                    'date' => date('Y-m-d')
                ];
            
                $rate = $ratesTable->newEntity($rateData);
                $ratesTable->save($rate);
                $io->info("Updated rate for {$code}");
                Cache::delete('currency_rates_with_changes', 'redis');
                $io->info('Cache invalidated');
            }
        }
        $io->success('Currency rates updated');
    }
}