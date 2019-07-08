<?php

namespace App\Entity;

use App\Entity\Provider\ProviderInterface;

/**
 * Валюты, их список и свойства.
 *
 * @property-read string $title     Название валюты.
 * @property-read string $name      Название валюты на родном языке.
 * @property-read string $code      Код валюты (см. https://ru.wikipedia.org/wiki/ISO_4217).
 * @property-read string $icob      Значок валюты.
 * @property-read string $wiki      Ссылка на вики по валюте.
 * @property-read array  $countries Список стран. где используется валюта ("title" - название, "wiki" - ссылка на вики по стране).
 */
class Currency
{
    /** Australian Dollar */
    const AUD = 'AUD';

    /** Euro */
    const EUR = 'EUR';

    /** Azerbaijanian Manat */
    const AZN = 'AZN';

    /** Lek */
    const ALL = 'ALL';

    /** Algerian Dinar */
    const DZD = 'DZD';

    /** US Dollar */
    const USD = 'USD';

    /** East Caribbean Dollar */
    const XCD = 'XCD';

    /** Kwanza */
    const AOA = 'AOA';

    /** Argentine Peso */
    const ARS = 'ARS';

    /** Armenian Dram */
    const AMD = 'AMD';

    /** Aruban Guilder */
    const AWG = 'AWG';

    /** Afghani */
    const AFN = 'AFN';

    /** Bahamian Dollar */
    const BSD = 'BSD';

    /** Taka */
    const BDT = 'BDT';

    /** Barbados Dollar */
    const BBD = 'BBD';

    /** Bahraini Dinar */
    const BHD = 'BHD';

    /** Belize Dollar */
    const BZD = 'BZD';

    /** Belarussian Ruble */
    const BYN = 'BYN';

    /** CFA Franc BCEAO */
    const XOF = 'XOF';

    /** Bermudian Dollar */
    const BMD = 'BMD';

    /** Bulgarian Lev */
    const BGN = 'BGN';

    /** Boliviano */
    const BOB = 'BOB';

    /** Convertible Mark */
    const BAM = 'BAM';

    /** Pula */
    const BWP = 'BWP';

    /** Brazilian Real */
    const BRL = 'BRL';

    /** Brunei Dollar */
    const BND = 'BND';

    /** Burundi Franc */
    const BIF = 'BIF';

    /** Ngultrum */
    const BTN = 'BTN';

    /** Vatu */
    const VUV = 'VUV';

    /** Forint */
    const HUF = 'HUF';

    /** Bolivar[14] */
    const VEF = 'VEF';

    /** Dong */
    const VND = 'VND';

    /** CFA Franc BEAC */
    const XAF = 'XAF';

    /** Gourde */
    const HTG = 'HTG';

    /** Guyana Dollar */
    const GYD = 'GYD';

    /** Dalasi */
    const GMD = 'GMD';

    /** Ghana Cedi */
    const GHS = 'GHS';

    /** Quetzal */
    const GTQ = 'GTQ';

    /** Guinea Franc */
    const GNF = 'GNF';

    /** Pound Sterling */
    const GBP = 'GBP';

    /** Gibraltar Pound */
    const GIP = 'GIP';

    /** Lempira */
    const HNL = 'HNL';

    /** Hong Kong Dollar */
    const HKD = 'HKD';

    /** Danish Krone */
    const DKK = 'DKK';

    /** Lari */
    const GEL = 'GEL';

    /** Djibouti Franc */
    const DJF = 'DJF';

    /** Dominican Peso */
    const DOP = 'DOP';

    /** Egyptian Pound */
    const EGP = 'EGP';

    /** Zambian Kwacha */
    const ZMW = 'ZMW';

    /** Moroccan Dirham */
    const MAD = 'MAD';

    /** Zimbabwe Dollar */
    const ZWL = 'ZWL';

    /** New Israeli Sheqel */
    const ILS = 'ILS';

    /** Indian Rupee */
    const INR = 'INR';

    /** Rupiah */
    const IDR = 'IDR';

    /** Jordanian Dinar */
    const JOD = 'JOD';

    /** Iraqi Dinar */
    const IQD = 'IQD';

    /** Iranian Rial */
    const IRR = 'IRR';

    /** Iceland Krona */
    const ISK = 'ISK';

    /** Yemeni Rial */
    const YER = 'YER';

    /** Cape Verde Escudo */
    const CVE = 'CVE';

    /** Tenge */
    const KZT = 'KZT';

    /** Cayman Islands Dollar */
    const KYD = 'KYD';

    /** Riel */
    const KHR = 'KHR';

    /** Canadian Dollar */
    const CAD = 'CAD';

    /** Qatari Rial */
    const QAR = 'QAR';

    /** Kenyan Shilling */
    const KES = 'KES';

    /** Som */
    const KGS = 'KGS';

    /** Yuan Renminbi */
    const CNY = 'CNY';

    /** Colombian Peso */
    const COP = 'COP';

    /** Comoro Franc */
    const KMF = 'KMF';

    /** Congolese Franc */
    const CDF = 'CDF';

    /** North Korean Won */
    const KPW = 'KPW';

    /** Won */
    const KRW = 'KRW';

    /** Costa Rican Colon */
    const CRC = 'CRC';

    /** Cuban Peso */
    const CUP = 'CUP';

    /** Kuwaiti Dinar */
    const KWD = 'KWD';

    /** Netherlands Antillean Guilder */
    const ANG = 'ANG';

    /** Kip */
    const LAK = 'LAK';

    /** Loti */
    const LSL = 'LSL';

    /** Liberian Dollar */
    const LRD = 'LRD';

    /** Lebanese Pound */
    const LBP = 'LBP';

    /** Libyan Dinar */
    const LYD = 'LYD';

    /** Swiss Franc */
    const CHF = 'CHF';

    /** Mauritius Rupee */
    const MUR = 'MUR';

    /** Ouguiya */
    const MRO = 'MRO';

    /** Malagasy Ariary */
    const MGA = 'MGA';

    /** Pataca */
    const MOP = 'MOP';

    /** Denar */
    const MKD = 'MKD';

    /** Kwacha */
    const MWK = 'MWK';

    /** Malaysian Ringgit */
    const MYR = 'MYR';

    /** Rufiyaa */
    const MVR = 'MVR';

    /** Mexican Peso */
    const MXN = 'MXN';

    /** Mozambique Metical */
    const MZN = 'MZN';

    /** Moldovan Leu */
    const MDL = 'MDL';

    /** Tugrik */
    const MNT = 'MNT';

    /** Kyat */
    const MMK = 'MMK';

    /** Namibia Dollar */
    const NAD = 'NAD';

    /** Nepalese Rupee */
    const NPR = 'NPR';

    /** Naira */
    const NGN = 'NGN';

    /** Cordoba Oro */
    const NIO = 'NIO';

    /** New Zealand Dollar */
    const NZD = 'NZD';

    /** CFP Franc */
    const XPF = 'XPF';

    /** Norwegian Krone */
    const NOK = 'NOK';

    /** UAE Dirham */
    const AED = 'AED';

    /** Rial Omani */
    const OMR = 'OMR';

    /** Pakistan Rupee */
    const PKR = 'PKR';

    /** Balboa */
    const PAB = 'PAB';

    /** Kina */
    const PGK = 'PGK';

    /** Guarani */
    const PYG = 'PYG';

    /** Nuevo Sol */
    const PEN = 'PEN';

    /** Zloty */
    const PLN = 'PLN';

    /** Russian Ruble */
    const RUB = 'RUB';

    /** Rwanda Franc */
    const RWF = 'RWF';

    /** Romanian Leu */
    const RON = 'RON';

    /** El Salvador Colon */
    const SVC = 'SVC';

    /** Tala */
    const WST = 'WST';

    /** Dobra */
    const STD = 'STD';

    /** Saudi Riyal */
    const SAR = 'SAR';

    /** Lilangeni */
    const SZL = 'SZL';

    /** Saint Helena Pound */
    const SHP = 'SHP';

    /** Seychelles Rupee */
    const SCR = 'SCR';

    /** Serbian Dinar */
    const RSD = 'RSD';

    /** Singapore Dollar */
    const SGD = 'SGD';

    /** Syrian Pound */
    const SYP = 'SYP';

    /** Solomon Islands Dollar */
    const SBD = 'SBD';

    /** Somali Shilling */
    const SOS = 'SOS';

    /** Sudanese Pound */
    const SDG = 'SDG';

    /** Surinam Dollar */
    const SRD = 'SRD';

    /** Leone */
    const SLL = 'SLL';

    /** Somoni */
    const TJS = 'TJS';

    /** Baht */
    const THB = 'THB';

    /** New Taiwan Dollar */
    const TWD = 'TWD';

    /** Tanzanian Shilling */
    const TZS = 'TZS';

    /** Pa’anga */
    const TOP = 'TOP';

    /** Trinidad and Tobago Dollar */
    const TTD = 'TTD';

    /** Tunisian Dinar */
    const TND = 'TND';

    /** Turkmenistan New Manat */
    const TMT = 'TMT';

    /** Turkish Lira */
    const TRY = 'TRY';

    /** Uganda Shilling */
    const UGX = 'UGX';

    /** Uzbekistan Sum */
    const UZS = 'UZS';

    /** Hryvnia */
    const UAH = 'UAH';

    /** Peso Uruguayo */
    const UYU = 'UYU';

    /** Fiji Dollar */
    const FJD = 'FJD';

    /** Philippine Peso */
    const PHP = 'PHP';

    /** Falkland Islands Pound */
    const FKP = 'FKP';

    /** Kuna */
    const HRK = 'HRK';

    /** Czech Koruna */
    const CZK = 'CZK';

    /** Chilean Peso */
    const CLP = 'CLP';

    /** Swedish Krona */
    const SEK = 'SEK';

    /** Sri Lanka Rupee */
    const LKR = 'LKR';

    /** Nakfa */
    const ERN = 'ERN';

    /** Ethiopian Birr */
    const ETB = 'ETB';

    /** Rand */
    const ZAR = 'ZAR';

    /** South Sudanese Pound */
    const SSP = 'SSP';

    /** Jamaican Dollar */
    const JMD = 'JMD';

    /** Yen */
    const JPY = 'JPY';

    /** @var array Список валют. */
    protected static $currencies = null;

    /** @var string Валюта объекта. */
    protected $currency;

    /**
     * Конструктор.
     *
     * @param string $currency Код валюты.
     *
     * @throws App\Entity\Exception Если указан неизвестный код.
     */
    public function __construct(string $currency)
    {
        self::catalog();

        if (!array_key_exists($currency, self::$currencies)) {
            throw new Exception("Неизвестная валюта: {$currency}");
        }

        $this->currency = $currency;
    }

    /**
     * Getter.
     *
     * @param string $name Имя свойства валюты.
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return array_key_exists($name, self::$currencies[$this->currency])
            ? self::$currencies[$this->currency][$name]
            : null;
    }

    /**
     * Преобразование в строку.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->currency;
    }

    /**
     * Получение курса валюты.
     *
     * @param App\Entity\Provider\ProviderInterface  $provider    Источник курса.
     * @param App\Entity\Currency                    $currency    OPTIONAL По отношению к какой валюте получить курс (базовая для источника).
     * @param boolean                                $immediately OPTIONAL Получить курсы немедленно (без кэширования).
     *
     * return string
     */
    public function getRate(ProviderInterface $provider, self $currency = null, bool $immediately = false): string
    {
        return $provider->getRate($currency, $this, $immediately);
    }

    /**
     * Конвертирование.
     *
     * @param float                                  $amount      Исходная сумма.
     * @param App\Entity\ProviderInterface $provider              Источник курса.
     * @param App\Entity\Currency                    $currency    OPTIONAL Целевая валюта (базовая для источника).
     * @param boolean                                $immediately OPTIONAL Получить курсы немедленно (без кэширования).
     *
     * @return string
     */
    public function convert(float $amount, ProviderInterface $provider, self $currency = null, bool $immediately = false): string
    {
        return $provider->convert($amount, $currency, $this, $immediately);
    }

    /**
     * Конструктор.
     *
     * @param string $currency Код валюты.
     *
     * @return App\Entity\Currency
     */
    public static function get(string $currency): self
    {
        return new self($currency);
    }

    /**
     * Возврат списка всех известных валют.
     *
     * @return array Код валюты => её описание.
     */
    public static function catalog(): array
    {
        if (is_null(self::$currencies)) {
            self::$currencies = json_decode(file_get_contents(__DIR__ . '/../../assets/currencies.json'), true);
        }
        return self::$currencies;
    }
}
