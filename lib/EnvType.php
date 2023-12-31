<?php /** @noinspection PhpUnused */

namespace NotaTools;

/**
 * Class EnvType
 * @package NotaTools
 */
class EnvType
{
    public const PROD = 'prod';
    public const STAGE = 'stage';
    public const DEV = 'dev';

    /**
     * @return string
     */
    public static function getServerType(): string
    {
        if ((isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === self::DEV) || (isset($_SERVER['HTTP_APP_ENV']) && $_SERVER['HTTP_APP_ENV'] === self::DEV) || (isset($_COOKIE['DEV']) && $_COOKIE['DEV'] === BitrixUtils::BX_BOOL_TRUE) || getenv('APP_ENV') === self::DEV) {
            return self::DEV;
        }
        if ((isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === self::STAGE) || (isset($_SERVER['HTTP_APP_ENV']) && $_SERVER['HTTP_APP_ENV'] === self::STAGE) || (isset($_COOKIE['STAGE']) && $_COOKIE['STAGE'] === BitrixUtils::BX_BOOL_TRUE) || getenv('APP_ENV') === self::STAGE) {
            return self::STAGE;
        }
        return self::PROD;
    }

    /**
     * @return bool
     */
    public static function isProd(): bool
    {
        return self::getServerType() === self::PROD;
    }

    /**
     * @return bool
     */
    public static function isDev(): bool
    {
        return self::getServerType() === self::DEV;
    }

    /**
     * @return bool
     */
    public static function isStage(): bool
    {
        return self::getServerType() === self::STAGE;
    }

}
