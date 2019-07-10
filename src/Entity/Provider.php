<?php

namespace App\Entity;

use App\Entity\Provider\ProviderInterface;

use \ReflectionClass;

/**
 * Источник курса валют.
 */
class Provider
{
    /** @var array Список источников курсов: имя => [описание, имя класса] */
    protected static $providers = [];

    /**
     * Возврат экземпляра источника курсов.
     *
     * @throws App\Entity\Exception Если мимя провайдера неправильное.
     *
     * @return App\Entity\Provider\ProviderInterface
     */
    public static function getProvider(string $provider): ProviderInterface
    {
        static $providers = [];

        if (!array_key_exists($provider, self::getProviders())) {
            throw new Exception("Указан неизвестный тип источника курсов: {$provider}");
        }

        if (!array_key_exists($provider, $providers)) {
            $providerClass = self::$providers[$provider]['class'];
            $providers[$provider] = new $providerClass;
        }

        return $providers[$provider];
    }

    /**
     * Получение списка доступных провайдеров.
     *
     * @return array [Код => Описание]
     */
    public static function getProviders(): array
    {
        if (empty(self::$providers)) {
            foreach (glob(__DIR__ . '/Provider/*.php') as $file) {
                $name  = substr(basename($file), 0, -4);
                $class = __CLASS__ . "\\{$name}";
                try {
                    self::addProvider($name, $class);
                } catch (Exception $ex) {
                    unset($ex); // ignore
                }
            }
        }
        
        return self::$providers;
    }

    /**
     * Получение отсортированного по приоритету списка доступных провайдеров.
     *
     * @return array [id => Код]
     */
    public static function getSortedProviders(): array
    {
        foreach (self::getProviders() as $key => $item) {
            $provider = Provider::getProvider($key);
            $priority[$key] = $provider->getPriority();
            $providers[$key] = $item;
        }
        array_multisort($priority, SORT_ASC, $providers);
        return $providers;
    }

    /**
     * Добавление источника курсов валют.
     *
     * @param string $name  Имя источника.
     * @param string $class Имя класса
     * @param string $desc  Описание.
     *
     * @throws App\Entity\Exception
     */
    public static function addProvider(string $name, string $class, string $desc = null)
    {
        if (array_key_exists($class, self::$providers)) {
            // ignore
        } elseif (!class_exists($class)) {
            throw new Exception("Класс {$class} не найден.");
        } elseif (!is_subclass_of($class, ProviderInterface::class)) {
            throw new Exception("Класс {$class} не предоставляет интерфейс источника курсов валют.");
        } elseif (empty($reflection = new ReflectionClass($class))) {
            throw new Exception("Не удалось получить описание класса {$class}.");
        } elseif ($reflection->isAbstract()) {
            throw new Exception("Класс {$class} является абстрактным.");
        } else {
            self::$providers[$name] = [
                'class' => $class,
                'title' => $desc ?: trim($reflection->getDocComment(), " \t\n\r\0\x0B/*")
            ];
        }
    }
}
