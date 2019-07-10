<?php

namespace App\Entity\Provider;

use App\Entity\Currency;
use App\Entity\Exception;

/**
 * Абстракция для источников курсов валют.
 */
abstract class AbstractProvider implements ProviderInterface
{
    /** @var integer Точность операций BC Math. */
    protected $scale = null;

    /** @var integer Время жизни кэша. */
    protected $ttl = null;

    /** @var integer Приоритет опроса источника. */
    protected $priority = null;

    /** @var array Кэш. */
    protected static $cache = [];

    /**
     * Конструктор.
     *
     * @param integer $scale OPTIONAL Точность операций BC Math.
     * @param integer $ttl   OPTIONAL Время жизни кэша.
     */
    public function __construct(int $scale = self::DEFAULT_SCALE, int $ttl = self::DEFAULT_TTL, int $priority = self::DEFAULT_PRIORITY)
    {
        $this->setScale($scale);
        $this->setTimeToLive($ttl);
        $this->setPriority($priority);
    }

    /**
     * Установка точности операций BC Math.
     *
     * @param integer $scale Значение.
     *
     * @throws App\Entity\Exception
     *
     * @return $this
     */
    public function setScale(int $scale): ProviderInterface
    {
        if ($scale < 0) {
            throw new Exception("Неправильное значение точности: {$scale}.");
        }

        if ($scale != $this->scale) {
            self::$cache[get_called_class()] = [];
        }

        $this->scale = (int) $scale;
        return $this;
    }

    /**
     * Установка времени жизни кэша.
     *
     * @param integer $ttl Значение.
     *
     * @throws App\Entity\Exception
     *
     * @return $this
     */
    public function setTimeToLive(int $ttl): ProviderInterface
    {
        if ($ttl < 0) {
            throw new Exception("Неправильное значение TTL: {$ttl}.");
        }

        $this->ttl = $ttl;
        return $this;
    }

    /**
     * Установка времени приоритета опроса источника.
     *
     * @param integer $priority Значение.
     *
     * @throws App\Entity\Exception
     *
     * @return $this
     */
    public function setPriority(int $priority): ProviderInterface
    {
        if ($priority < 0) {
            throw new Exception("Неправильное значение PRIORITY: {$priority}.");
        }

        $this->priority = $priority;
        return $this;
    }

    /**
     * @return int $priority Значение.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }


    /**
     * Возврат всех курсов валют по отношению к указанной.
     * Если валюта не указана - используется базовая для данного источника курсов.
     * Список будет содержать записи вида: 1 единица валюты = X единиц базовой (или указанной).
     * Например, источник для евро будет содержать записи вида: 1RUB = 1/60EUR, 1EUR = 1.1USD и т.п.
     *
     * @param App\Entity\Currency $currency OPTIONAL Валюта.
     * @param boolean $immediately          OPTIONAL Получить курсы немедленно (без кэширования).
     *
     * @throws App\Entity\Exception
     *
     * @return array Код валюты (1 единица) = X единиц указанной.
     */
    public function getRates(Currency $currency = null, bool $immediately = false): array
    {
        $base = $this->getBase();
        if (empty($currency)) {
            $currency = $base;
        }

        $rates = $this->getSourceData($immediately);
        $currencies = $this->getCurrencies();
        if (!in_array($currency->code, $currencies)) {
            throw new Exception("Валюта {$currency->code} не поддерживается этим источником.");
        }

        $provider = get_called_class();
        if ($immediately
            or !array_key_exists($provider, self::$cache)
            or !array_key_exists($currency->code, self::$cache[$provider])
        ) {
            // пусть источник в RUB:  1USD = 60RUB, 1RUB=1RUB
            // тогда результат в USD: 1USD = 1USD, 1RUB=1/60USD
            // т.е. все значения нужно разделить на курс указанной валюты по источнику
            $divider = $rates[$currency->code];
            self::$cache[$provider][$currency->code] = array_map(function ($rate) use ($divider) {
                return bcdiv($rate, $divider, $this->scale);
            }, $rates);
        }

        return self::$cache[$provider][$currency->code];
    }

    /**
     * Возврат значения стоимости 1 единицы указанной валюты в исходной (базовой).
     * Т.е. если X = getRate(USD, RUB), это означает 1 USD = X RUB.
     *
     * @param App\Entity\Currency $target Целевая валюта (чей курс ищем).
     * @param App\Entity\Currency $source OPTIONAL Исходная (базовая) валюта.
     * @param boolean $immediately        OPTIONAL Получить курсы немедленно (без кэширования).
     *
     * @throws App\Entity\Exception
     *
     * @return string
     */
    public function getRate(Currency $target, Currency $source = null, bool $immediately = false): string
    {
        $rates = $this->getRates($source, $immediately);
        if (!array_key_exists($target->code, $rates)) {
            throw new Exception("Валюта {$target->code} не поддерживается этим источником.");
        }

        return $rates[$target->code];
    }

    /**
     * Получение списка доступных курсов валют.
     *
     * @param boolean $immediately OPTIONAL Получить список немедленно (без кэширования).
     *
     * @return array
     */
    public function getCurrencies(bool $immediately = false): array
    {
        return array_keys($this->getSourceData($immediately));
    }

    /**
     * Конвертирование суммы в указанную валюту в указанной (базовой).
     * Если X = convert(100, USD, RUB), это значит 100 RUB = X USD.
     *
     * @param float               $amount      Исходная сумма.
     * @param App\Entity\Currency $target      Целевая валюта.
     * @param App\Entity\Currency $source      OPTIONAL Исходная (базовая) валюта.
     * @param boolean             $immediately OPTIONAL Получить курсы немедленно (без кэширования).
     *
     * @return string
     */
    public function convert(float $amount, Currency $target, Currency $source = null, bool $immediately = false)
    {
        $rate = $this->getRate($target, $source, $immediately);
        return bcdiv($amount, $rate, $this->scale);
    }

    /**
     * Собственно получение курсов.
     *
     * @param boolean $immediately OPTIONAL Получить курсы немедленно (без кэширования).
     *
     * @return array Код валюты (1 единица) = X единиц базовой.
     */
    protected function getSourceData(bool $immediately = false): array
    {
        static $cached = [];

        $provider = get_called_class();
        $file     = $this->getCacheFile();
        if (file_exists($file) and ($immediately or filemtime($file) < (time() - $this->ttl))) {
            unset($cached[$provider]);
        }

        if ($immediately and array_key_exists($provider, $cached)) {
            unset($cached[$provider]);
        }

        if (array_key_exists($provider, $cached)) {
            return $cached[$provider];
        }

        $data  = $this->getSourceCode();
        $rates = $this->parseSource($data);
        $base  = (string) $this->getBase();
        if (array_key_exists($base, $rates) and 1.0 !== (float) $rates[$base]) {
            throw new Exception("Ошибка определения базовой валюты источника курса: {$base}.");
        } else {
            $rates[$base] = 1;
        }

        foreach ($rates as $currency => $rate) {
            try {
                $currency = (string) new Currency($currency);
            } catch (Exception $ex) {
                unset($rates[$currency], $ex);
                continue; // ignore
            }
            if (false === strpos($rate, '.')) {
                $rate .= '.0';
            }
            if (!preg_match('#^(\d*)?(\.\d*)$#', $rate)) {
                throw new Exception("Неправильное значение курса: {$currency} = {$base}.");
            } else {
                $rates[$currency] = bcdiv($rate, 1, self::DEFAULT_SCALE);
            }
        }

        $cached[$provider] = $rates;
        return $cached[$provider];
    }

    /**
     * Получение кода (XML) от источника курсов.
     *
     * @throws App\Entity\Exception
     *
     * @return string
     */
    protected function getSourceCode(): string
    {
        $file = $this->getCacheFile();
        if (file_exists($file) and filemtime($file) > (time() - $this->ttl)) {
            return file_get_contents($file);
        }

        $request = curl_init();
        curl_setopt_array($request, [
            CURLOPT_URL             => $this->getSource(),
            CURLOPT_TIMEOUT         => 10,
            CURLOPT_FOLLOWLOCATION  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => false,
            CURLOPT_NOBODY          => false,
            CURLOPT_HTTPGET         => true,
            CURLOPT_HTTPHEADER      => ['Expect:'],
        ]);
        $response = [
            'data'    => trim(curl_exec($request)),
            'error'   => curl_error($request),
            'errno'   => curl_errno($request),
            'headers' => curl_getinfo($request),
        ];
        curl_close($request);

        if (0 !== $response['errno']) {
            throw new Exception("Ошибка выполнения запроса: curl-error={$response['error']}.");
        } elseif (!is_array($response['headers']) or !array_key_exists('http_code', $response['headers'])) {
            throw new Exception("Ошибка определения кода ответа.");
        } elseif (200 !== $response['headers']['http_code']) {
            throw new Exception("Недопустимый код ответа: http=error={$response['headers']['http_code']}.");
        } elseif (empty($response['data'])) {
            throw new Exception("От источника курсов получен пустой ответ.");
        }

        file_put_contents($file, $response['data']);
        return $response['data'];
    }

    /**
     * Кэшированный файл данных от провайдера.
     *
     * @return string
     */
    protected function getCacheFile(): string
    {
        return (sys_get_temp_dir() . '/' . preg_replace('#\W#', '_', get_called_class()) . '.tmp');
    }

    /**
     * Возврат URL к источнику для получения курсов.
     *
     * @return string
     */
    abstract protected function getSource(): string;

    /**
     * Парсер данных полученных из URL источника курсов.
     *
     * @param string $source Полученный от источника файл.
     *
     * @return array Код валюты (1 единица) = X единиц базовой.
     */
    abstract protected function parseSource(string $source): array;
}
