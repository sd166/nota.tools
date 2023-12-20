<?php namespace NotaTools\Settings;

/**
 * Class ConfigCache
 * @package NotaTools\Settings
 */
class ConfigCache
{
    public const TYPES = [
        'FILES'     => 'files',
        'MEMCACHE'  => 'memcache',
        'MEMCACHED' => 'memcached',
        'REDIS'     => 'redis',
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
     * @var string
     */
    protected $sessid = '';


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
        if(!empty($params['TYPE']) && !in_array($params['TYPE'], static::TYPES)){
            throw new \InvalidArgumentException('Неверный тип кеша');
        }
        $this->type = $params['TYPE'];
        $this->sessid = $params['SESSID'];
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
    public function getSessid(): string
    {
        return $this->sessid ?: '';
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
            'TYPE'   => (string)getenv('CACHE_TYPE'),
            'SESSID' => $_SERVER['DOCUMENT_ROOT'] . '_' . getenv('COMPOSE_PROJECT_NAME').'_'.getenv('APP_ENV').'_'.getenv('BRANCH_PULL'),
            'HOST'   => (string)getenv('CACHE_INTERFACE') ?: '',
            'PORT'   => (string)getenv('CACHE_PORT') ?: '',
        ];
        $this->setParams($params);
        $this->loaded = true;
    }

}