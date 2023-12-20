<?php namespace NotaTools\Settings;

include("/home/bitrix/www/local/modules/nota.tools/lib/Settings/ConfigDb.php");
include("/home/bitrix/www/local/modules/nota.tools/lib/Settings/ConfigCache.php");
include("/home/bitrix/www/local/modules/nota.tools/lib/Settings/ConfigSession.php");

use Dotenv\Dotenv;

/**
 * Class Config
 * @package NotaTools\Settings
 */
class Config
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
     * @var ConfigDb
     */
    protected $dbParams;
    /**
     * @var bool
     */
    protected $debug = false;
    /**
     * @var bool
     */
    protected $clearPort = false;
    /**
     * @var string
     */
    protected $pullSignatureKey = '';
    /**
     * @var string
     */
    protected $cryptoKey = '';
    /**
     * @var string
     */
    protected $composerFile = '/var/www/composer.json';
    /**
     * @var ConfigCache
     */
    protected $cacheParams;
    /**
     * @var ConfigSession
     */
    protected $sessionParams;

    /**
     * Config constructor.
     *
     * @param string $envFolder
     */
    public function __construct(string $envFolder = '')
    {
        if (!$this->loaded) {
            if (empty($envFolder)) {
                $envFolder = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
            }
            $this->loadEnv($envFolder);
        }
    }

    /**
     * @param string $envFolder
     *
     * @return Config
     */
    public static function getInstance(string $envFolder): Config
    {
        if (!static::$instance) {
            static::$instance = new static($envFolder);
        }
        return static::$instance;
    }

    /**
     * @param $envFolder
     */
    public function loadEnv($envFolder): void
    {
        $dotEnv = Dotenv::create($envFolder);
        $dotEnv->load();
        $dotEnv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']);
        $this->loadParams();
        $this->dbParams = new ConfigDb();
        $this->cacheParams = new ConfigCache();
        $this->sessionParams = new ConfigSession();
        $this->loaded = true;
    }

    /**
     * @return ConfigDb
     */
    public function getDbParams(): ConfigDb
    {
        return $this->dbParams;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function isClearPort(): bool
    {
        return $this->clearPort;
    }

    /**
     * @return string
     */
    public function getPullSignatureKey(): string
    {
        return $this->pullSignatureKey;
    }

    /**
     * @return string
     */
    public function getCryptoKey(): string
    {
        return $this->cryptoKey;
    }

    /**
     * @return string
     */
    public function getComposerFile(): string
    {
        return $this->composerFile;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->debug = (bool)getenv('DEBUG');
        $this->clearPort = (bool)getenv('CLEAR_PORT');
        $this->pullSignatureKey = $params['PULL_SIGNATURE_KEY'];
        $this->cryptoKey = $params['CRYPTO_KEY'];
        $this->composerFile = $params['COMPOSER_SERVER_FILE'];
    }

    /**
     * @return ConfigSession
     */
    public function getSessionParams(): ConfigSession
    {
        return $this->sessionParams;
    }

    /**
     * @return ConfigCache
     */
    public function getCacheParams(): ConfigCache
    {
        return $this->cacheParams;
    }

    protected function loadParams()
    {
        $params = [
            'DEBUG'                => (bool)getenv('DEBUG'),
            'CLEAR_PORT'           => (bool)getenv('CLEAR_PORT'),
            'PULL_SIGNATURE_KEY'   => (string)getenv('PULL_SIGNATURE_KEY'),
            'CRYPTO_KEY'           => (string)getenv('CRYPTO_KEY'),
            'COMPOSER_SERVER_FILE' => (string)getenv('COMPOSER_SERVER_FILE'),
        ];
        $this->setParams($params);
    }
}
