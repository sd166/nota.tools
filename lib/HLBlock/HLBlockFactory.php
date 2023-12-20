<?php /** @noinspection PhpUnused */

namespace NotaTools\HLBlock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use NotaTools\Exception\HLBlock\HLBlockException;
use NotaTools\Exception\HLBlock\HLBlockMoreThanOneException;
use NotaTools\Exception\HLBlock\HLBlockNotFoundException;

/**
 * Class HLBlockFactory
 * @package NotaTools\HLBlock
 */
class HLBlockFactory
{
    /**
     * Возвращает скомпилированную сущность HL-блока по имени его сущности.
     *
     * @param string $hlBlockName
     *
     * @return DataManager
     * @throws HLBlockMoreThanOneException
     * @throws HLBlockException
     * @throws HLBlockNotFoundException
     */
    public static function createTableObject($hlBlockName): DataManager
    {
        return self::doCreateTableObject(['=NAME' => $hlBlockName]);
    }

    /**
     * Возвращает скомпилированную сущность HL-блока по имени его таблицы в базе данных.
     *
     * @param string $tableName
     *
     * @return DataManager
     * @throws HLBlockMoreThanOneException
     * @throws HLBlockException
     * @throws HLBlockNotFoundException
     */
    public static function createTableObjectByTable($tableName): DataManager
    {
        return self::doCreateTableObject(['=TABLE_NAME' => $tableName]);
    }

    /**
     * Возвращает скомпилированную сущность HL-блока по имени его таблицы в базе данных.
     *
     * @param int $id
     *
     * @return DataManager
     * @throws HLBlockMoreThanOneException
     * @throws HLBlockException
     * @throws HLBlockNotFoundException
     */
    public static function createTableObjectById($id): DataManager
    {
        return self::doCreateTableObject(['=ID' => $id]);
    }

    /**
     * Возвращает скомпилированную сущность HL-блока по заданному фильтру, но фильтр должен в итоге находить один
     * HL-блок.
     *
     * @param array $filter
     *
     * @return DataManager
     * @throws HLBlockMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws HLBlockException
     */
    private static function doCreateTableObject(array $filter): DataManager
    {
        try {
            Loader::includeModule('highloadblock');
            $result = (new Query(HighloadBlockTable::getEntity()))->setFilter($filter)->setSelect(['*'])->exec();
        } catch (LoaderException|ArgumentException|SystemException $e) {
            throw new HLBlockException($e->getMessage());
        }
        if ($result->getSelectedRowsCount() > 1) {
            throw new HLBlockMoreThanOneException('Неверный фильтр: найдено несколько HLBlock.');
        }
        $hlBlockFields = $result->fetch();
        if (!is_array($hlBlockFields)) {
            throw new HLBlockNotFoundException();
        }
        try {
            $dataManager = HighloadBlockTable::compileEntity($hlBlockFields)->getDataClass();
        } catch (SystemException $e) {
            throw new HLBlockException($e->getMessage());
        }
        if (is_string($dataManager)) {
            return new $dataManager;
        }
        if (is_object($dataManager)) {
            return $dataManager;
        }
        throw new HLBlockException('Ошибка компиляции сущности для HLBlock.');
    }
}
