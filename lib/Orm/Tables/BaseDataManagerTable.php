<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;

/**
 * Class BaseDataManager
 * @package NotaTools\Orm\Tables
 */
class BaseDataManagerTable extends DataManager
{
    //fix to ElementTable
    public const TYPE_TEXT = 'text';
    public const TYPE_HTML = 'html';

    /**
     * @var
     */
    public static $customEntity;

    /**
     * @var array
     */
    public static $map;
    /**
     * @var string
     */
    public static $tableName;
    /**
     * @var string
     */
    public static $objectClass;
    /**
     * @var string
     */
    public static $collectionClass;
    /**
     * @var string
     */
    public static $ufId;

    /**
     * @return array
     */
    public static function getMap()
    {
        return static::$map;
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return static::$tableName;
    }

    /**
     * @return EntityObject|EntityObject[]|string
     */
    public static function getObjectClass()
    {
        return static::$objectClass;
    }

    public static function getCollectionClass()
    {
        return static::$collectionClass;
    }

    /**
     * @return null
     */
    public static function getUfId()
    {
        return static::$ufId;
    }

    /**
     * @return Entity
     */
    public static function getEntity()
    {
        return static::$customEntity;
    }

    /**
     * @param DataManager|string $className
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function init($className): void
    {
        static::$map = $className::getMap();
        static::$tableName = $className::getTableName();
        static::$objectClass = $className::getObjectClass();
        static::$collectionClass = $className::getCollectionClass();
        static::$ufId = $className::getUfId();
        static::$customEntity = $className::getEntity();
    }
}