<?php

namespace App\Entity\Provider;

use App\Entity\Currency;

/**
 * Центральный Банк Российской Федерации
 */
class RussianCentralBank extends AbstractProvider
{
    /**
     * Возврат базовой валюты источника курса.
     * Т.е. в списке, полученном с источника указано: 1 единица валюты = X единиц базовой.
     *
     * @return App\Entity\Currency
     */
    public function getBase(): Currency
    {
        $currency = null;
        if (is_null($currency)) {
            $currency = new Currency(Currency::RUB);
        }
        return $currency;
    }

    /**
     * Возврат URL к источнику для получения курсов.
     *
     * @return string
     */
    protected function getSource(): string
    {
        return 'https://www.cbr-xml-daily.ru/daily_json.js';
    }

    /**
     * Парсер данных полученных из URL источника курсов.
     *
     * @param string $source Полученный от источника файл.
     *
     * @return array Код валюты (1 единица) = X единиц базовой.
     */
    protected function parseSource(string $source): array
    {
        // источник хранит информацию вида:
        // 1 EUR = 70 RUB, 100 INR = 90 RUB
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $source);
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        
        $jsonData = json_decode(curl_exec($curlSession));
        curl_close($curlSession);
        $rates = [];
        foreach ($jsonData->Valute as $rate) {
            $code    = (string) $rate->CharCode[0];
            $nominal = (int) $rate->Nominal[0];
            $value   = strtr($rate->Value[0], [',' => '.']);
            $rates[$code] = bcdiv($value, $nominal, self::DEFAULT_SCALE);
        }
        return $rates;
    }
}
