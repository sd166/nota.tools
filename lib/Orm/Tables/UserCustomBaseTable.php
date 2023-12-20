<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables;

use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\UserTable;
use NotaTools\Orm\Collection\UserCustomBaseCollection;
use NotaTools\Orm\Model\UserBase;

/**
 * Class UserCustomBaseTable
 * @package NotaTools\Orm\Tables
 */
class UserCustomBaseTable extends UserTable
{
    /**
     * @inheritDoc
     */
    public static function getObjectClass()
    {
        return UserBase::class;
    }

    /**
     * @return Collection|string
     */
    public static function getCollectionClass()
    {
        return UserCustomBaseCollection::class;
    }

    /**
     * @inheritDoc
     */
    public static function add(array $data)
    {
        BaseDataManagerTable::init(static::class);
        return BaseDataManagerTable::add($data);
    }

    /**
     * @inheritDoc
     */
    public static function update($primary, array $data)
    {
        BaseDataManagerTable::init(static::class);
        return BaseDataManagerTable::update($primary, $data);
    }

    /**
     * @inheritDoc
     */
    public static function delete($primary)
    {
        BaseDataManagerTable::init(static::class);
        return BaseDataManagerTable::delete($primary);
    }
}