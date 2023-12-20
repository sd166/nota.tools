<?php

namespace NotaTools\Events;

use NotaTools\Helpers\TaggedCacheHelper;

/**
 * Class IblockCacheHelpersEvents
 * @package NotaTools\Events
 */
class IblockCacheHelpersEvents extends AbstractEvents
{

    public static function getEvents(): array
    {
        return [
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterIBlockPropertyDelete',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterIBlockPropertyAdd',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterIBlockPropertyUpdate',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterIBlockAdd',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterIBlockUpdate',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterIBlockDelete',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
        ];
    }

    public static function clearCacheHelper(): void
    {
        TaggedCacheHelper::clearManagedCache(['iblock_property_helper', 'iblock_helper']);
    }
}