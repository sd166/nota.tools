<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables;

use Bitrix\Main\FileTable;

/**
 * Class FileCustomBaseTable
 * @package NotaTools\Orm\Tables
 */
class FileCustomBaseTable extends FileTable
{
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