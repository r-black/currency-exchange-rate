<?php

namespace App\Entity\Provider;

use App\Entity\Currency;

/**
 * Интерфейс источника курсов.
 */
interface ProviderInterface
{
    /** Точность операций BC Math по умолчанию. */
    const DEFAULT_SCALE = 8;

    /** Время жизни кэша по умолчанию. */
    const DEFAULT_TTL = 300;

    /** Приоритет опроса источника по умолчанию. */
    const DEFAULT_PRIORITY = 0;

    /**
     * Конструктор.
     *
     * @param integer $scale OPTIONAL Точность операций BC Math.
     * @param integer $ttl   OPTIONAL Время жизни кэша.
     */
    public function __construct(int $scale = self::DEFAULT_SCALE, int $ttl = self::DEFAULT_TTL, int $priority = self::DEFAULT_PRIORITY);

    /**
     * Установка точности операций BC Math.
     *
     * @param integer $scale Значение.
     *
     * @return $this
     */
    public function setScale(int $scale): ProviderInterface;

    /**
     * Установка времени жизни кэша.
     *
     * @param integer $ttl Значение.
     *
     * @return $this
     */
    public function setTimeToLive(int $ttl): ProviderInterface;

    /**
     * Установка приоритета.
     *
     * @param integer $priority Значение.
     *
     * @return $this
     */
    public function setPriority(int $priority): ProviderInterface;

    /**
     * Возврат базовой валюты источника курса.
     * Т.е. в списке, полученном с источника указано: 1 единица валюты = X единиц базовой.
     *
     * @return App\Entity\Currency
     */
    public function getBase(): Currency;

    /**
     * Возврат всех курсов валют по отношению к указанной.
     * Если валюта не укзана - используется базовая для данного источника курсов.
     * Список будет содержать записи вида: 1 единица валюты = X единиц базовой (или указанной).
     * Например, источник для евро будет содержать записи вида: 1RUB = 1/60EUR, 1EUR = 1.1USD и т.п.
     *
     * @param App\Entity\Currency $currency OPTIONAL Валюта.
     * @param boolean $immediately          OPTIONAL Получить курсы немедленно (без кэширования).
     *
     * @return array Код валюты (1 единица) = X единиц указанной.
     */
    public function getRates(Currency $currency = null, bool $immediately = false): array;

    /**
     * Возврат значения стоимости 1 единицы указанной валюты в исходной (базовой).
     * Т.е. если X = getRate(USD, RUB), это означает 1 USD = X RUB.
     *
     * @param App\Entity\Currency $target Целевая валюта (чей курс ищем).
     * @param App\Entity\Currency $source OPTIONAL Исходная (базовая) валюта.
     * @param boolean $immediately        OPTIONAL Получить курсы немедленно (без кэширования).
     *
     * @return string
     */
    public function getRate(Currency $target, Currency $source = null, bool $immediately = false): string;

    /**
     * Получение списка доступных курсов валют.
     *
     * @param boolean $immediately OPTIONAL Получить список немедленно (без кэширования).
     *
     * @return array
     */
    public function getCurrencies(bool $immediately = false): array;

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
    public function convert(float $amount, Currency $target, Currency $source = null, bool $immediately = false);
}
