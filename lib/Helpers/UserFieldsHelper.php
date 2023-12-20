<?php /** @noinspection PhpUnused */

namespace NotaTools\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use NotaTools\Exception\UserField\UserFieldEnumNotFoundException;
use NotaTools\Exception\UserField\UserFieldNotFoundException;
use NotaTools\Orm\Tables\UserField\EO_UserFieldCustom;
use NotaTools\Orm\Tables\UserField\EO_UserFieldEnumCustom;
use NotaTools\Orm\Tables\UserField\UserFieldCustomTable;
use NotaTools\Orm\Tables\UserField\UserFieldEnumCustomTable;

/**
 * Class UserFieldsHelper
 *
 * @package NotaTools\Helpers
 */
class UserFieldsHelper
{
    /** @var array|EO_UserFieldCustom[] */
    private static $props;
    /** @var array|EO_UserFieldEnumCustom[] */
    private static $enums = [];

    /**
     * @param string $entityID
     * @param string $code
     *
     * @return EO_UserFieldCustom
     * @throws UserFieldNotFoundException
     */
    public static function getPropByCode(string $entityID, string $code): EO_UserFieldCustom
    {
        if (self::$props[$entityID] === null) {
            self::loadProps($entityID);
        }
        if (!array_key_exists($code, self::$props[$entityID])) {
            throw new UserFieldNotFoundException();
        }
        return self::$props[$entityID][$code];
    }

    /**
     * @param string $entityID
     * @param string $code
     *
     * @return int
     * @throws UserFieldNotFoundException
     */
    public static function getPropIdByCode(string $entityID, string $code): int
    {

        return self::getPropByCode($entityID, $code)->getId();
    }

    /**
     * @param string $entityID
     * @param int    $id
     *
     * @return string
     * @throws UserFieldNotFoundException
     */
    public static function getPropCodeById(string $entityID, int $id): string
    {

        return self::getPropById($entityID, $id)->getXmlId();
    }

    /**
     * @param string $entityID
     * @param string $propCode
     * @param        $xmlId
     *
     * @return int
     * @throws UserFieldNotFoundException
     * @throws UserFieldEnumNotFoundException
     */
    public static function getEnumIdByXmlId(string $entityID, string $propCode, $xmlId): int
    {
        return self::getEnumByXmlId($entityID, $propCode, $xmlId)->getId();
    }

    /**
     * @param string $entityID
     * @param string $propCode
     * @param        $xmlId
     *
     * @return EO_UserFieldEnumCustom
     * @throws UserFieldNotFoundException
     * @throws UserFieldEnumNotFoundException
     */
    public static function getEnumByXmlId(string $entityID, string $propCode, $xmlId): EO_UserFieldEnumCustom
    {
        if (!isset(self::$enums[$entityID][$propCode])) {
            self::loadEnumVals($entityID, $propCode);
        }
        if (!array_key_exists($propCode, self::$enums[$entityID])) {
            throw new UserFieldNotFoundException();
        }
        if (!array_key_exists($xmlId, self::$enums[$entityID][$propCode])) {
            throw new UserFieldEnumNotFoundException();
        }
        return self::$enums[$entityID][$propCode][$xmlId];
    }

    /**
     * @param string $entityID
     * @param string $propCode
     * @param int    $id
     *
     * @return EO_UserFieldEnumCustom
     * @throws UserFieldNotFoundException
     * @throws UserFieldEnumNotFoundException
     */
    public static function getEnumById(string $entityID, string $propCode, int $id): EO_UserFieldEnumCustom
    {
        if (!isset(self::$enums[$entityID][$propCode])) {
            self::loadEnumVals($entityID, $propCode);
        }
        if (!array_key_exists($propCode, self::$enums[$entityID])) {
            throw new UserFieldNotFoundException();
        }
        /** @var EO_UserFieldEnumCustom $loadEnumVal */
        foreach (self::$enums[$entityID][$propCode] as $loadEnumVal) {
            if ($loadEnumVal->getId() === $id) {
                return $loadEnumVal;
            }
        }
        throw new UserFieldEnumNotFoundException();
    }

    /**
     * @param string $entityID
     * @param string $propCode
     * @param        $val
     *
     * @return EO_UserFieldEnumCustom
     * @throws UserFieldNotFoundException
     * @throws UserFieldEnumNotFoundException
     */
    public static function getEnumByIdOrXmlId(string $entityID, string $propCode, $val): EO_UserFieldEnumCustom
    {
        if (is_numeric($val)) {
            $val = (int)$val;
            return static::getEnumById($entityID, $propCode, $val);
        }
        if (is_string($val) && !empty($val)) {
            return static::getEnumByXmlId($entityID, $propCode, $val);
        }
        throw new UserFieldEnumNotFoundException();
    }

    /**
     * @param string $entityID
     * @param string $propCode
     *
     * @return array|EO_UserFieldEnumCustom[]
     * @throws UserFieldNotFoundException
     * @throws UserFieldEnumNotFoundException
     */
    public static function getPropsEnum(string $entityID, string $propCode): array
    {
        if (!isset(self::$enums[$entityID][$propCode])) {
            self::loadEnumVals($entityID, $propCode);
        }
        return self::$enums[$entityID][$propCode] ?: [];
    }

    /**
     * @param string $entityID
     * @param int    $id
     *
     * @return EO_UserFieldCustom
     * @throws UserFieldNotFoundException
     */
    public static function getPropById(string $entityID, int $id): EO_UserFieldCustom
    {
        if (self::$props[$entityID] === null) {
            self::loadProps($entityID);
        }
        /** @var EO_UserFieldCustom $prop */
        foreach (self::$props[$entityID] as $prop) {
            if ($prop->getId() === $id) {
                return $prop;
            }
        }
        throw new UserFieldNotFoundException();
    }

    /**
     * @param string $entityID
     * @param string $propCode
     *
     * @throws UserFieldNotFoundException
     * @throws UserFieldEnumNotFoundException
     */
    protected static function loadEnumVals(string $entityID, string $propCode): void
    {
        try {
            $query = UserFieldEnumCustomTable::query();
            $query->where('USER_FIELD_ID', self::getPropIdByCode($entityID, $propCode));
            $res = $query->setSelect(['*'])->exec();
            /** @var EO_UserFieldEnumCustom $item */
            while ($item = $res->fetchObject()) {
                self::$enums[$entityID][$propCode][$item->getXmlId()] = $item;
            }
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new UserFieldEnumNotFoundException('Значения не найдены для свойства - ' . $propCode . ' - ' . $e->getMessage());
        }
    }

    /**
     *
     * @param string $entityID
     *
     * @throws UserFieldNotFoundException
     */
    protected static function loadProps(string $entityID): void
    {
        self::$props[$entityID] = [];
        try {
            $query = UserFieldCustomTable::query();
            $query->where('ENTITY_ID', $entityID);
            $res = $query->setSelect(['*'])->exec();
            /** @var EO_UserFieldCustom $item */
            while ($item = $res->fetchObject()) {
                self::$props[$entityID][$item->getFieldName()] = $item;
            }
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new UserFieldNotFoundException();
        }
    }
}