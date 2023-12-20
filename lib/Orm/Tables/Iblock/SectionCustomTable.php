<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables\Iblock;

use Bitrix\Iblock\EO_Section_Query;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\SystemException;
use NotaTools\Exception\Iblock\IblockException;
use NotaTools\Exception\Iblock\IblockNotFoundException;
use NotaTools\Iblock\IblockHelper;
use NotaTools\Orm\Tables\BaseDataManagerTable;
use NotaTools\Orm\Tables\UserCustomBaseTable;

/**
 * Class SectionCustomTable
 * @package NotaTools\Orm\Tables\Iblock
 */
class SectionCustomTable extends SectionTable
{
    public const IBLOCK_TYPE = '';
    public const IBLOCK_CODE = '';

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap()
    {
        $map = parent::getMap();
        $map['CREATED_BY_USER'] = new Reference('CREATED_BY_USER', UserCustomBaseTable::class, ['=this.CREATED_BY' => 'ref.ID'],
            ['join_type' => 'LEFT']);
        $map['MODIFIED_BY_USER'] = new Reference('MODIFIED_BY_USER', UserCustomBaseTable::class, ['=this.MODIFIED_BY' => 'ref.ID'],
            ['join_type' => 'LEFT']);
        return $map;
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

    /**
     * @return string|null
     * @throws ArgumentException
     * @throws IblockNotFoundException
     * @throws IblockException
     */
    public static function getUfId()
    {
        $iblockId = static::getIblockId();
        if($iblockId > 0) {
            return 'IBLOCK_' . $iblockId . '_SECTION';
        }
        return null;
    }

    /**
     * @return int
     * @throws ArgumentException
     * @throws IblockNotFoundException
     * @throws IblockException
     */
    public static function getIblockId(): int
    {
        if (!empty(static::IBLOCK_TYPE) && !empty(static::IBLOCK_CODE)) {
            return IblockHelper::getIblockId(static::IBLOCK_TYPE, static::IBLOCK_CODE);
        }
        return 0;
    }

    /**
     * @return EO_Section_Query|Query
     * @throws ArgumentException
     * @throws IblockException
     * @throws IblockNotFoundException
     * @throws SystemException
     */
    public static function query()
    {
        $query = new Query(static::getEntity());
        $iblockId = static::getIblockId();
        if ($iblockId > 0) {
            $query->where('IBLOCK_ID', $iblockId);
        }
        return $query;
    }
}