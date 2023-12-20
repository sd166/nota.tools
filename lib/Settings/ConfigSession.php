<?php namespace NotaTools\Settings;

/**
 * Class ConfigSession
 * @package NotaTools\Settings
 */
class ConfigSession
{
    public const TYPES = [
        'FILES'    => 'files',
        'DATABASE' => 'database',
        'MEMCACHE' => 'memcache',
        'REDIS'    => 'redis',
    ];
    /**
     * @var static
     */
    protected static $instance;
    /**
     * @var bool
     */
    protected $loaded = false;
    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $host = '';
    /**
     * @var string
     */
    protected $port = '';

    /**
     * Config constructor.
     *
     */
    public function __construct()
    {
        if (!$this->loaded) {
            $this->loadParams();
        }
    }

    /**
     * @return Config
     */
    public static function getInstance(): Config
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        if (!empty($params['TYPE']) && !in_array($params['TYPE'], static::TYPES)) {
            throw new \InvalidArgumentException('Неверный тип хранения сессий');
        }
        $this->type = $params['TYPE'];
        $this->host = $params['HOST'];
        $this->port = $params['PORT'];
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host ?: '';
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port ?: '';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type ?: '';
    }

    /**
     * @return string
     */
    public function getActualType(): string
    {
        $type = $this->getType();
        if (empty($type)) {
            $type = static::TYPES['FILES'];
        }
        return $type;
    }

    protected function loadParams(): void
    {
        $params = [
            'TYPE' => (string)getenv('SESSION_TYPE'),
            'HOST' => (string)getenv('SESSION_INTERFACE') ?: '',
            'PORT' => (string)getenv('SESSION_PORT') ?: '',
        ];
        $this->setParams($params);
        $this->loaded = true;
    }

}