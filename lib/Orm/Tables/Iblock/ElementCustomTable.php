<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables\Iblock;

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\EO_Element_Query;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Fields\EnumField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\SystemException;
use NotaTools\Exception\Iblock\IblockException;
use NotaTools\Exception\Iblock\IblockNotFoundException;
use NotaTools\Iblock\IblockHelper;
use NotaTools\Orm\Tables\BaseDataManagerTable;
use NotaTools\Orm\Tables\UserCustomBaseTable;

/**
 * Class ElementCustomTable
 * @package NotaTools\Orm\Tables\Iblock
 */
class ElementCustomTable extends ElementTable
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
        $map['WF_LOCKED_BY_USER'] = new Reference('WF_LOCKED_BY_USER', UserCustomBaseTable::class, ['=this.WF_LOCKED_BY' => 'ref.ID'],
            ['join_type' => 'LEFT']);
        /** @var EnumField $enumField */
        $enumField = $map['DETAIL_TEXT_TYPE'];
        $map['DETAIL_TEXT_TYPE'] = new EnumField('DETAIL_TEXT_TYPE', [
            'values'        => [static::TYPE_TEXT, static::TYPE_HTML],
            'default_value' => static::TYPE_TEXT,
            'title'         => $enumField->getTitle(),
        ]);
        /** @var EnumField $enumField */
        $enumField = $map['PREVIEW_TEXT_TYPE'];
        $map['PREVIEW_TEXT_TYPE'] = new EnumField('PREVIEW_TEXT_TYPE', [
            'values'        => [static::TYPE_TEXT, static::TYPE_HTML],
            'default_value' => static::TYPE_TEXT,
            'title'         => $enumField->getTitle(),
        ]);
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
     * @return EO_Element_Query|Query|EO_ElementCustom_Query
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