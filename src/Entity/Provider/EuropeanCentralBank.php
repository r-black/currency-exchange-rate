<?php

namespace App\Entity\Provider;

use App\Entity\Currency;
use App\Entity\Exception;

use \SimpleXMLElement;

/**
 * European Central Bank
 */
class EuropeanCentralBank extends AbstractProvider
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
            $currency = new Currency(Currency::EUR);
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
        return 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    }

    /**
     * Парсер данных полученных из URL источника курсов.
     *
     * @param string $source Полученный от источника файл.
     *
     * @throws App\Entity\Exception
     *
     * @return array Код валюты (1 единица) = X единиц базовой.
     */
    protected function parseSource(string $source): array
    {
        // источник хранит информацию вида:
        // 1 EUR = 70 RUB, 1 EUR = 80 INR
        $xml = new SimpleXMLElement($source);
        $envelope = $xml->xpath('/gesmes:Envelope');
        if (0 === count($envelope)) {
            throw new Exception("Ошибки парсинга ответа.");
        } else {
            $envelope = reset($envelope);
        }

        $children = $envelope->children();
        if (0 === count($children)) {
            throw new Exception("Ошибки парсинга ответа.");
        } else {
            $children = reset($children)->children();
        }

        if (0 === count($children)) {
            throw new Exception("Ошибки парсинга ответа.");
        } else {
            $children = reset($children)->children();
        }

        $rates = [];
        foreach ($children as $rate) {
            $attrs = $rate->attributes();
            $rates["{$attrs->currency}"] = bcdiv(1, (string) $attrs->rate, $this->scale);
        }
        return $rates;
    }
}
