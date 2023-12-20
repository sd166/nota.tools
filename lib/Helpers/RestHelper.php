<?php

namespace NotaTools\Helpers;

use Bitrix\Main\LoaderException;
use NotaTools\Exception\Rest\RestCriticalModuleException;

/**
 * Class RestHelper
 * @package Sevensuns\Utils\Helpers
 */
class RestHelper
{
    /**
     * @param string $module
     *
     * @throws RestCriticalModuleException
     */
    public static function includeModule(string $module): void
    {
        try {
            LoaderHelper::includeModule($module);
        } catch (LoaderException $e) {
            throw new RestCriticalModuleException($e->getMessage());
        }
    }
}