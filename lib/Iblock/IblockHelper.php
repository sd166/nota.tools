<?php /** @noinspection PhpUnused */

namespace NotaTools\Iblock;

use Bitrix\Iblock\IblockFieldTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use NotaTools\Enum\CacheTimeEnum;
use NotaTools\Exception\Iblock\IblockException;
use NotaTools\Exception\Iblock\IblockFieldSettingsException;
use NotaTools\Exception\Iblock\IblockNotFoundException;
use NotaTools\Helpers\TaggedCacheHelper;

/**
 * Class IblockHelper
 * @package NotaTools\Iblock
 */
class IblockHelper
{
    /**
     * @var array
     */
    private static $iblockInfo;

    /**
     * @var array
     */
    private static $propertyIdIndex = [];

    /**
     * Возвращает id инфоблока по его типу и символьному коду
     *
     * @param string $type
     * @param string $code
     *
     * @return int
     * @throws ArgumentException
     * @throws IblockException
     * @throws IblockNotFoundException
     */
    public static function getIblockId($type, $code): int
    {
        return (int)self::getIblockField($type, $code, 'ID');
    }

    /**
     * Возвращает xml id инфоблока по его типу и символьному коду
     *
     * @param $type
     * @param $code
     *
     * @return string
     * @throws ArgumentException
     * @throws IblockException
     * @throws IblockNotFoundException
     */
    public static function getIblockXmlId($type, $code): string
    {
        return trim(self::getIblockField($type, $code, 'XML_ID'));
    }

    public static function getIblockDescription($type, $code): string
    {
        return trim(self::getIblockField($type, $code, 'DESCRIPTION'));
    }

    /**
     * Проверка существования типа инфоблоков
     *
     * @param string $typeID
     *
     * @return bool
     * @throws ArgumentException
     * @throws IblockException
     */
    public static function isIblockTypeExists($typeID): bool
    {
        $typeID = trim($typeID);
        if (empty($typeID)) {
            throw new ArgumentException('Iblock type id must be specified');
        }
        try {
            return 1 === TypeTable::query()->setSelect(['ID'])->setFilter(['=ID' => $typeID])->setLimit(1)->exec()->getSelectedRowsCount();
        } catch (ArgumentException|SystemException $e) {
            throw new IblockException($e->getMessage());
        }
    }

    /**
     * @param $iblockId
     *
     * @return array|false
     * @throws IblockFieldSettingsException
     * @throws IblockNotFoundException
     * @throws ArgumentException
     * @throws IblockException
     */
    public static function getIblockCodeSettingsById($iblockId)
    {
        $iblockId = (int)$iblockId;
        if ($iblockId <= 0) {
            throw new ArgumentException('Идентификатор инфоблока не является числом, большим 0', 'iblockId');
        }
        try {
            $query = IblockTable::query();
            $query->where((new ConditionTree())->where('ID', $iblockId)->where('IBLOCK_FIELDS.FIELD_ID', 'CODE'));
            $queryResult = $query->setSelect([
                'ID',
                'CODE_REQUIRED' => 'IBLOCK_FIELDS.IS_REQUIRED',
                'CODE_SETTINGS' => 'IBLOCK_FIELDS.DEFAULT_VALUE',
            ])->registerRuntimeField(new ReferenceField('IBLOCK_FIELDS', IblockFieldTable::getEntity(),
                Join::on('this.ID', 'ref.IBLOCK_ID')))->exec();
            if ($item = $queryResult->fetch()) {
                $item['CODE_SETTINGS'] = unserialize($item['CODE_SETTINGS'], false);
                if ($item['CODE_SETTINGS'] === false) {
                    throw new IblockFieldSettingsException();
                }
                return $item;
            }
        } catch (ArgumentException|SystemException $e) {
            throw new IblockException($e->getMessage());
        }
        throw new IblockNotFoundException();
    }

    /**
     * @param $type
     * @param $code
     * @param $field
     *
     * @return string
     * @throws IblockNotFoundException
     * @throws IblockException
     * @throws ArgumentException
     */
    private static function getIblockField($type, $code, $field): string
    {
        $type = trim($type);
        $code = trim($code);
        if (empty($type) || empty($code)) {
            throw new ArgumentException('Iblock type and code must be specified');
        }
        $allIblockInfo = self::getAllIblockInfo();
        if (isset($allIblockInfo[$type][$code])) {
            return trim($allIblockInfo[$type][$code][$field]);
        }
        throw new IblockNotFoundException(sprintf('Iblock `%s\%s` not found', $type, $code));

    }

    /**
     * Возвращает краткую информацию обо всех инфоблоках в виде многомерного массива.
     *
     * @param bool $force
     *
     * @return array <iblock type> => <iblock code> => array of iblock fields
     * @throws IblockException
     */
    private static function getAllIblockInfo(bool $force = false): array
    {
        if (self::$iblockInfo === null || empty(self::$iblockInfo)) {
            try {
                $iblockInfo = [];
                if(!$force){
                    $cache = Application::getInstance()->getCache();
                    $cacheDir = '/iblock/helper';
                    $cacheUniqueString = 'iblock_helper';
                    $cacheTag = 'iblock_helper';
                    if ($cache->initCache(CacheTimeEnum::MONTH, $cacheUniqueString, $cacheDir)) {
                        $vars = $cache->getVars();
                        $iblockInfo = $vars['RES'];
                    } elseif ($cache->startDataCache()) {
                        $tagCache = new TaggedCacheHelper($cacheDir);
                        $iblockInfo = static::getIblockInfoData();
                        $tagCache->addTag($cacheTag);
                        $tagCache->end();
                        $cache->endDataCache(['RES' => $iblockInfo]);
                    }
                } else {
                    $iblockInfo = static::getIblockInfoData();
                }
                self::$iblockInfo = $iblockInfo;
            } catch (ArgumentException|SystemException $e) {
                throw new IblockException($e->getMessage());
            }
        }
        return self::$iblockInfo;
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    private static function getIblockInfoData(): array
    {
        $iblockInfo = [];
        $iblockList = IblockTable::query()->setSelect(['ID', 'IBLOCK_TYPE_ID', 'CODE', 'XML_ID', 'DESCRIPTION'])->exec();
        while ($iblock = $iblockList->fetch()) {
            $iblockInfo[$iblock['IBLOCK_TYPE_ID']][$iblock['CODE']] = $iblock;
        }
        return $iblockInfo;
    }
}
