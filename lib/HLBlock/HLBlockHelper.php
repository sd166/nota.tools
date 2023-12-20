<?php /** @noinspection PhpUnused */

namespace NotaTools\HLBlock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\EO_UserField;
use Bitrix\Main\EO_UserField_Collection;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserFieldTable;
use NotaTools\Exception\HLBlock\HLBlockException;
use NotaTools\Exception\HLBlock\HLBlockFieldNotFoundException;
use NotaTools\Exception\HLBlock\HLBlockMoreThanOneException;
use NotaTools\Exception\HLBlock\HLBlockNotFoundException;

/**
 * Class HLBlockHelper
 * @package NotaTools\HLBlock
 */
class HLBlockHelper
{
    /**
     * @var array
     */
    protected static $fields = [];
    /**
     * @var array
     */
    protected static $ids = [];

    /**
     * @param                         $val
     * @param string                  $hlName
     *
     * @param DataManager|string|null $dataManger
     *
     * @return mixed|null
     * @throws HLBlockMoreThanOneException
     * @throws HLBlockException
     * @throws HLBlockNotFoundException
     */
    public static function getHlValByIdOrXmlId($val, string $hlName, $dataManger = null)
    {
        if (!empty($val)) {
            if (is_numeric($val)) {
                return static::getHlValById((int)$val, $hlName, $dataManger);
            }
            if (is_string($val)) {
                return static::getHlValByXmlId((string)$val, $hlName, $dataManger);
            }
        }
        return null;
    }

    /**
     * @param int                     $val
     * @param string                  $hlName
     *
     * @param DataManager|string|null $dataManger
     *
     * @return mixed|null
     * @throws HLBlockMoreThanOneException
     * @throws HLBlockException
     * @throws HLBlockNotFoundException
     */
    public static function getHlValById(
        int $val,
        string $hlName,
        $dataManger = null
    ) {
        if ($dataManger === null) {
            $dataManger = HLBlockFactory::createTableObject($hlName);
        }
        try {
            return $dataManger::getById($val)->fetchObject();
        } catch (ArgumentException|SystemException $e) {
            throw new HLBlockException($e->getMessage());
        }
    }

    /**
     * @param string                  $hlName
     *
     * @param DataManager|string|null $dataManger
     *
     * @return mixed|null
     * @throws HLBlockMoreThanOneException
     * @throws HLBlockException
     * @throws HLBlockNotFoundException
     */
    public static function getHlVals(string $hlName, $dataManger = null)
    {
        if ($dataManger === null) {
            $dataManger = HLBlockFactory::createTableObject($hlName);
        }
        try {
            return $dataManger::query()->setSelect(['*'])->setOrder(['ID' => 'ASC'])->exec()->fetchCollection();
        } catch (ArgumentException|SystemException $e) {
            throw new HLBlockException($e->getMessage());
        }
    }

    /**
     * @param string                  $val
     * @param string                  $hlName
     *
     * @param DataManager|string|null $dataManger
     *
     * @return mixed|null
     * @throws HLBlockMoreThanOneException
     * @throws HLBlockException
     * @throws HLBlockNotFoundException
     */
    public static function getHlValByXmlId(string $val, string $hlName, $dataManger = null)
    {
        if ($dataManger === null) {
            $dataManger = HLBlockFactory::createTableObject($hlName);
        }
        try {
            $query = $dataManger::query();
            $query->where('UF_XML_ID', $val);
            return $query->setSelect(['*'])->exec()->fetchObject();
        } catch (ArgumentException|SystemException $e) {
            throw new HLBlockException($e->getMessage());
        }
    }

    /**
     * @param string $hlName
     *
     * @return mixed
     * @throws HLBlockException
     */
    public static function getHlBlockIdByName(string $hlName)
    {
        if (static::$ids[$hlName] === null) {
            try {
                $query = HighloadBlockTable::query();
                $query->where('NAME', $hlName);
                $hlItem = $query->setSelect(['ID'])->exec()->fetchObject();
                static::$ids[$hlName] = $hlItem->getId();
            } catch (ArgumentException|SystemException $e) {
                throw new HLBlockException($e->getMessage());
            }
        }
        return static::$ids[$hlName];
    }

    /**
     * @param string $hlName
     * @param string $fieldsName
     *
     * @return mixed
     * @throws HLBlockException
     */
    public static function getHlBlockFieldIdByCode(string $hlName, string $fieldsName)
    {
        if (static::$fields[$hlName] === null) {
            static::loadHlBlockFields($hlName);
        }
        if (!array_key_exists($fieldsName, static::$fields[$hlName])) {
            throw new HLBlockFieldNotFoundException();
        }
        /** @var EO_UserField $field */
        $field = static::$fields[$hlName][$fieldsName];
        return $field->getId();
    }

    /**
     * @param string $hlName
     *
     * @throws HLBlockException
     */
    protected static function loadHlBlockFields(string $hlName): void
    {
        try {
            $hlId = static::getHlBlockIdByName($hlName);
            $query = UserFieldTable::query();
            $query->where((new ConditionTree())->where('ENTITY_ID', 'HLBLOCK_' . $hlId));
            /** @var EO_UserField_Collection $collection */
            $collection = $query->setSelect(['*'])->exec()->fetchCollection();
            static::$fields[$hlName] = [];
            foreach ($collection as $item) {
                static::$fields[$hlName][$item->getFieldName()] = $item;
            }
        } catch (ArgumentException|SystemException $e) {
            throw new HLBlockException($e->getMessage());
        }
    }
}