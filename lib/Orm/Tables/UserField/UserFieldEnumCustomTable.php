<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables\UserField;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use NotaTools\Constructor\EntityConstructor;

/**
 * Class UserFieldEnumCustomTable
 * @package NotaTools\Orm\Tables\UserField
 */
class UserFieldEnumCustomTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'b_user_field_enum';
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        /** @todo поставить статичный маппинг - эта таблица неизменна */
        return EntityConstructor::compileEntityDataClass('UserFieldEnumCustomGen', static::getTableName())::getMap();
    }
}