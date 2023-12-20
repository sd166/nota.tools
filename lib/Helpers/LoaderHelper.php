<?php

namespace NotaTools\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

/**
 * Class LoaderHelper
 * @package NotaTools\Helpers
 */
class LoaderHelper
{
    /**
     * @param string $module
     *
     * @throws LoaderException
     */
    public static function includeModule(string $module): void
    {
        if (!Loader::includeModule($module)) {
            throw new LoaderException('модуль ' . $module . ' не загружен');
        }
    }
}