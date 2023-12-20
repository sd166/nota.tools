<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables\Iblock;

use Bitrix\Iblock\SectionElementTable;
use NotaTools\Orm\Tables\BaseDataManagerTable;

/**
 * Class SectionElementCustomTable
 * @package NotaTools\Tables\Iblock
 */
class SectionElementCustomTable extends SectionElementTable
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