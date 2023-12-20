<?php

namespace NotaTools\Events;

use NotaTools\Helpers\TaggedCacheHelper;

/**
 * Class UserCacheHelpersEvents
 * @package NotaTools\Events
 */
class UserCacheHelpersEvents extends AbstractEvents
{

    public static function getEvents(): array
    {
        return [
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterGroupAdd',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterGroupUpdate',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
            [
                'FROM'   => 'iblock',
                'EVENT'  => 'OnAfterGroupDelete',
                'CLASS'  => static::class,
                'METHOD' => 'clearCacheHelper',
            ],
        ];
    }

    public static function clearCacheHelper(): void
    {
        TaggedCacheHelper::clearManagedCache(['user_group_helper']);
    }
}