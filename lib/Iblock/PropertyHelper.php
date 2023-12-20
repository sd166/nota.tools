<?php /** @noinspection PhpUnused */

namespace NotaTools\Iblock;

use Bitrix\Iblock\EO_Property;
use Bitrix\Iblock\EO_PropertyEnumeration;
use Bitrix\Iblock\EO_SectionProperty_Collection;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\SectionPropertyTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use NotaTools\BitrixUtils;
use NotaTools\Enum\CacheTimeEnum;
use NotaTools\Exception\Iblock\IblockPropertyNotFoundException;
use NotaTools\Exception\Iblock\PropertyEnumNotFoundException;
use NotaTools\Helpers\TaggedCacheHelper;

/**
 * Class PropertyHelper
 *
 * @package NotaTools\Iblock
 */
class PropertyHelper
{
    public const BASE_PROPERTY_SELECT = [
        'ID',
        'IBLOCK_ID',
        'NAME',
        'ACTIVE',
        'SORT',
        'CODE',
        'DEFAULT_VALUE',
        'PROPERTY_TYPE',
        'LIST_TYPE',
        'MULTIPLE',
        'XML_ID',
        'FILE_TYPE',
        'TMP_ID',
        'LINK_IBLOCK_ID',
        'WITH_DESCRIPTION',
        'SEARCHABLE',
        'FILTRABLE',
        'IS_REQUIRED',
        'USER_TYPE',
        'USER_TYPE_SETTINGS_LIST',
        'USER_TYPE_SETTINGS',
        'HINT',
//                    'LINK_IBLOCK',
//                    'IBLOCK',
    ];
    public const BASE_ENUM_SELECT = ['*', 'PROPERTY.ID', 'PROPERTY.IBLOCK_ID'];
    /** @var array|EO_Property[] */
    private static $props;
    /** @var array|EO_SectionProperty_Collection */
    private static $facetProps;
    /** @var array|EO_PropertyEnumeration[] */
    private static $enums = [];

    /**
     * @param string $code
     *
     * @param int    $iblockId
     *
     * @return EO_Property
     * @throws IblockPropertyNotFoundException
     */
    public static function getPropByCode(int $iblockId, string $code): EO_Property
    {
        if (self::$props === null || empty(self::$props)) {
            self::loadProps();
        }
        if (($iblockId > 0) && !array_key_exists($code, self::$props[$iblockId])) {
            throw new IblockPropertyNotFoundException();
        }
        return self::$props[$iblockId][$code];
    }

    /**
     * @param int    $iblockId
     * @param string $code
     *
     * @return int
     * @throws IblockPropertyNotFoundException
     */
    public static function getPropIdByCode(int $iblockId, string $code): int
    {

        return self::getPropByCode($iblockId, $code)->getId();
    }

    /**
     * @param int $iblockId
     * @param int $id
     *
     * @return string
     * @throws IblockPropertyNotFoundException
     */
    public static function getPropCodeById(int $iblockId, int $id): string
    {

        return self::getPropById($iblockId, $id)->getCode();
    }

    /**
     * @param int    $iblockId
     * @param string $propCode
     * @param        $xmlId
     *
     * @return int
     * @throws IblockPropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getEnumIdByXmlId(int $iblockId, string $propCode, $xmlId): int
    {
        return self::getEnumByXmlId($iblockId, $propCode, $xmlId)->getId();
    }

    /**
     * @param int    $iblockId
     * @param string $propCode
     * @param        $xmlId
     *
     * @return EO_PropertyEnumeration
     * @throws IblockPropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getEnumByXmlId(int $iblockId, string $propCode, $xmlId): EO_PropertyEnumeration
    {
        if (!isset(self::$enums[$iblockId][$propCode])) {
            self::loadEnumVals($iblockId, $propCode);
        }
        if (!array_key_exists($propCode, self::$enums[$iblockId])) {
            throw new IblockPropertyNotFoundException();
        }
        if (!array_key_exists($xmlId, self::$enums[$iblockId][$propCode])) {
            throw new PropertyEnumNotFoundException();
        }
        return self::$enums[$iblockId][$propCode][$xmlId];
    }

    /**
     * @param int    $iblockId
     * @param string $propCode
     * @param int    $id
     *
     * @return EO_PropertyEnumeration
     * @throws IblockPropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getEnumById(int $iblockId, string $propCode, int $id): EO_PropertyEnumeration
    {
        if (!isset(self::$enums[$iblockId][$propCode])) {
            self::loadEnumVals($iblockId, $propCode);
        }
        if (!array_key_exists($propCode, self::$enums[$iblockId])) {
            throw new IblockPropertyNotFoundException();
        }
        /** @var EO_PropertyEnumeration $loadEnumVal */
        foreach (self::$enums[$iblockId][$propCode] as $loadEnumVal) {
            if ($loadEnumVal->getId() === $id) {
                return $loadEnumVal;
            }
        }
        throw new PropertyEnumNotFoundException();
    }

    /**
     * @param int    $iblockId
     * @param string $propCode
     * @param        $val
     *
     * @return EO_PropertyEnumeration
     * @throws IblockPropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getEnumByIdOrXmlId(int $iblockId, string $propCode, $val): EO_PropertyEnumeration
    {
        if (is_numeric($val)) {
            $val = (int)$val;
            return static::getEnumById($iblockId, $propCode, $val);
        }
        if (is_string($val) && !empty($val)) {
            return static::getEnumByXmlId($iblockId, $propCode, $val);
        }
        throw new PropertyEnumNotFoundException();
    }

    /**
     * @param int    $iblockId
     * @param string $propCode
     *
     * @return array|EO_PropertyEnumeration[]
     * @throws IblockPropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getPropsEnum(int $iblockId, string $propCode, array $order = ['SORT' => 'ASC']): array
    {
        if (!isset(self::$enums[$iblockId][$propCode])) {
            self::loadEnumVals($iblockId, $propCode, $order);
        }
        return self::$enums[$iblockId][$propCode] ?: [];
    }

    /**
     * @param int    $iblockId
     * @param string $propCode
     * @param array  $order
     *
     * @return array
     * @throws IblockPropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getPropsEnumShortList(int $iblockId, string $propCode, array $order = ['SORT' => 'ASC']): array
    {
        $result = [];
        $enums = static::getPropsEnum($iblockId, $propCode, $order);
        foreach ($enums as $enum) {
            $result[$enum->getId()] = $enum->getXmlId();
        }
        return $result;
    }

    /**
     * @param int    $iblockId
     * @param string $propCode
     *
     * @return array|EO_PropertyEnumeration[]
     * @throws IblockPropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getPropsEnumIds(int $iblockId, string $propCode): array
    {
        $ids = [];
        $enums = static::getPropsEnum($iblockId, $propCode);
        foreach ($enums as $enum) {
            $ids[] = $enum->getId();
        }
        return $ids;
    }

    /**
     * @param int $id
     *
     * @param int $iblockId
     *
     * @return EO_Property
     * @throws IblockPropertyNotFoundException
     */
    public static function getPropById(int $iblockId, int $id): EO_Property
    {
        if (self::$props === null || empty(self::$props)) {
            self::loadProps();
        }
        if ($iblockId > 0) {
            /** @var EO_Property $prop */
            foreach (self::$props[$iblockId] as $prop) {
                if ($prop->getId() === $id) {
                    return $prop;
                }
            }
        }
        throw new IblockPropertyNotFoundException();
    }

    /**
     * @param int $iblockId
     *
     * @return array|EO_Property[]
     * @throws IblockPropertyNotFoundException
     */
    public static function getPropertiesByIblock(int $iblockId): array
    {
        if (self::$props === null || empty(self::$props)) {
            self::loadProps();
        }
        return self::$props[$iblockId];
    }

    /**
     * @param int $iblockId
     * @param int $sectionId
     *
     * @return EO_SectionProperty_Collection|null
     * @throws IblockPropertyNotFoundException
     */
    public static function getFacetProps(int $iblockId, int $sectionId = 0): ?EO_SectionProperty_Collection
    {
        if (self::$facetProps === null) {
            self::loadFacetProps($iblockId, $sectionId);
        }
        return self::$facetProps[$iblockId][$sectionId];
    }

    /**
     * @param int $iblockId
     * @param int $sectionId
     *
     * @return array
     * @throws IblockPropertyNotFoundException
     */
    public static function getFacetPropsIds(int $iblockId, int $sectionId = 0): array
    {
        $props = static::getFacetProps($iblockId, $sectionId);
        return $props !== null ? $props->getPropertyIdList() : [];
    }

    /**
     * @param int    $iblockId
     * @param string $propCode
     *
     * @throws IblockPropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    protected static function loadEnumVals(int $iblockId, string $propCode, $order = ['SORT' => 'ASC']): void
    {
        try {
            $query = PropertyEnumerationTable::query();
            $propId = self::getPropIdByCode($iblockId, $propCode);
            $query->where('PROPERTY_ID', $propId);
            $res = $query->setSelect(static::BASE_ENUM_SELECT)->setOrder($order)->exec();
            $count = $res->getSelectedRowsCount();
            /** @var EO_PropertyEnumeration $item */
            while ($item = $res->fetchObject()) {
                self::$enums[$item->getProperty()->getIblockId()][$propCode][$item->getXmlId()] = $item;
            }
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new PropertyEnumNotFoundException('Значения не найдены для свойства - ' . $propCode . ' - ' . $e->getMessage());
        }
    }

    /**
     * @throws IblockPropertyNotFoundException
     */
    protected static function loadProps(): void
    {
        $props = [];
        try {
            $cache = Application::getInstance()->getCache();
            $cacheDir = '/iblock/property_helper';
            $cacheUniqueString = 'iblock_property_helper';
            $cacheTag = 'iblock_property_helper';
            if ($cache->initCache(CacheTimeEnum::MONTH, $cacheUniqueString, $cacheDir)) {
                $vars = $cache->getVars();
                $propsData = $vars['RES'];
                if (!empty($propsData)) {
                    foreach ($propsData as $item) {
                        /** @var EO_Property $obj */
                        $obj = EO_Property::wakeUp($item);
                        $props[$obj->getIblockId()][$obj->getCode()] = $obj;
                    }
                    unset($obj);
                }
                unset($propsData);
            } elseif ($cache->startDataCache()) {
                $tagCache = new TaggedCacheHelper($cacheDir);
                $res = PropertyTable::query()->setSelect(static::BASE_PROPERTY_SELECT)->exec();
                /** @var EO_Property $item */
                while ($item = $res->fetchObject()) {
                    $props[$item->getIblockId()][$item->getCode()] = $item;
                }
                $propsData = [];
                foreach ($props as $iblockId => $iblockProps) {
                    foreach ($iblockProps as $obj) {
                        /** @var EO_Property $obj */
                        $item = $obj->collectValues();
                        $propsData[] = $item;
                    }
                }
                $tagCache->addTag($cacheTag);
                $tagCache->end();
                $cache->endDataCache(['RES' => $propsData]);
            }
            self::$props = $props;
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new IblockPropertyNotFoundException();
        }
    }

    /**
     * @param int $iblockId
     * @param int $sectionId
     *
     * @throws IblockPropertyNotFoundException
     */
    protected static function loadFacetProps(int $iblockId, int $sectionId = 0): void
    {
        self::$facetProps = [];
        try {
            $query = SectionPropertyTable::query();
            $query->where((new ConditionTree())->where('SMART_FILTER', BitrixUtils::BX_BOOL_TRUE)
                ->where('IBLOCK_ID', $iblockId)
                ->where('SECTION_ID', $sectionId));
            $res = $query->setSelect(['*'])->exec();
            self::$facetProps[$iblockId][$sectionId] = $res->fetchCollection();
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new IblockPropertyNotFoundException();
        }
    }
}