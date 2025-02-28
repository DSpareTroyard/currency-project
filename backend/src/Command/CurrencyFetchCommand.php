<?php
declare(strict_types=1);

namespace App\Command;

use Cake\I18n\Date;

use Cake\Console\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

class CurrencyFetchCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $xml = simplexml_load_file('https://www.cbr.ru/scripts/XML_daily.asp');
        
        $currenciesTable = TableRegistry::getTableLocator()->get('Currencies');
        $ratesTable = TableRegistry::getTableLocator()->get('Rates');
        
        foreach ($xml->Valute as $valute) {
            $value = (string)$valute->Value;

            if (empty($value)) {
                $io->warning("Empty rate for currency: " . (string)$valute->CharCode);
                continue;
            }

            $rateValue = (float)str_replace(',', '.', $value);

            $currency = $currenciesTable->findOrCreate([
                'code' => (string)$valute->CharCode
            ], function ($entity) use ($valute) {
                $entity->name = (string)$valute->Name;
            });

            $rateData = [
                'currency_id' => $currency->id,
                'rate' => $rateValue,
                'date' => new Date('now'),
            ];
            
            $rate = $ratesTable->findOrCreate([
                'currency_id' => $currency->id,
                'date' => $rateData['date']
            ]);

            $existingRate = $ratesTable->find()
                ->where([
                    'currency_id' => $currency->id,
                    'date' => $rateData['date']
                ])
                ->first();

            if ($existingRate) {
                $rate = $ratesTable->patchEntity($existingRate, ['rate' => $rateValue]);
            } else {
                $rate = $ratesTable->newEntity($rateData);
            }
            
            $currentRate = str_replace(',', '.', (string)$valute->Value);
            if (!$rate) {
                $rate = $ratesTable->newEntity([
                    'currency_id' => $currency->id,
                    'date' => date('Y-m-d'),
                    'rate' => $currentRate
                ]);
            } else {
                $rate->rate = $currentRate;
            }
            
            $ratesTable->patchEntity($rate, $rateData);
            $ratesTable->save($rate);
        }
        $io->success('Currency rates updated');
    }
}