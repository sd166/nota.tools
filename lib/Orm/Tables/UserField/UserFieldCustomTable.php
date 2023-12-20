<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables\UserField;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use NotaTools\Constructor\EntityConstructor;

/**
 * Class UserFieldCustomTable
 * @package NotaTools\Orm\Tables\UserField
 */
class UserFieldCustomTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'b_user_field';
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        /** @todo поставить статичный маппинг - эта таблица неизменна */
        return EntityConstructor::compileEntityDataClass('UserFieldCustomGen', static::getTableName())::getMap();
    }
}