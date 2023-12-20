<?php namespace NotaTools\Settings;

/**
 * Class ConfigDb
 * @package NotaTools\Settings
 */
class ConfigDb
{
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
    protected $host = '';
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $user = '';
    /**
     * @var string
     */
    protected $password = '';
    /**
     * @var string
     */
    protected $userBitrix = '';
    /**
     * @var string
     */
    protected $passwordBitrix = '';

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
        $this->host = $params['HOST'];
        $this->name = $params['NAME'];
        $this->user = $params['USER'];
        $this->password = $params['PASSWORD'];
        $this->userBitrix = $params['USER_BITRIX'];
        $this->passwordBitrix = $params['PASSWORD_BITRIX'];
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
    public function getName(): string
    {
        return $this->name ?: '';
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user ?: '';
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password ?: '';
    }

    /**
     * @return string
     */
    public function getUserBitrix(): string
    {
        return $this->userBitrix ?: '';
    }

    /**
     * @return string
     */
    public function getPasswordBitrix(): string
    {
        return $this->passwordBitrix ?: '';
    }

    /**
     * @return string
     */
    public function getActualUser(): string
    {
        return $this->getUserBitrix() ?: $this->getUser();
    }

    /**
     * @return string
     */
    public function getActualPassword(): string
    {
        return $this->getPasswordBitrix() ?: $this->getPassword();
    }

    protected function loadParams(): void
    {
        $params = [
            'HOST'            => (string)getenv('DB_HOST'),
            'NAME'            => (string)getenv('DB_NAME'),
            'USER'            => (string)getenv('DB_USER'),
            'PASSWORD'        => (string)getenv('DB_PASSWORD'),
            'USER_BITRIX'     => (string)getenv('DB_USER_BITRIX'),
            'PASSWORD_BITRIX' => (string)getenv('DB_PASSWORD_BITRIX'),
        ];
        $this->setParams($params);
        $this->loaded = true;
    }
}