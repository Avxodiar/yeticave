<?php
namespace yeticave;

trait SingletonTrait
{
    private static $instance;

    private function __construct() { }

    /**
     * Возвращает экземпляр одиночки
     * @return self
     */
    public static function getInstance() : self
    {
        return self::$instance ?: self::$instance = new static();
    }

    /**
     * Клонирование одиночек не допустимо.
     * Клонирование и десериализация не разрешены для одиночек.

     * @return void
     */
    private function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    /**
     * Предотвращение сериализации экземпляра одиночки.
     * @return void
     */
    public function __sleep()
    {
        trigger_error('Serializing is not allowed.', E_USER_ERROR);
    }
    /**
     * Предотвращение десериализации экземпляра одиночки.
     * @return void
     */
    public function __wakeup()
    {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }
}
