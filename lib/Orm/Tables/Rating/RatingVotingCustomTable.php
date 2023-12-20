<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables\Rating;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\SystemException;
use NotaTools\Constructor\EntityConstructor;
use NotaTools\Orm\Tables\Iblock\ElementCustomTable;

/**
 * Class RatingVotingCustomTable
 * @package NotaTools\Orm\Tables\Rating
 */
class RatingVotingCustomTable extends DataManager
{

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'b_rating_voting';
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        /** @todo поставить статичный маппинг - эта таблица неизменна */
        $dataManager = EntityConstructor::compileEntityDataClass('RatingVotingCustomBase', static::getTableName());
        $map = $dataManager::getMap();
        $map['SUM'] = new ExpressionField('SUM' . 'SUM(%s)', 'TOTAL_VALUE');
        $map['ELEMENT'] = new Reference('ELEMENT', ElementCustomTable::class, ['=this.ENTITY_ID' => 'ref.ID'], ['join_type' => 'LEFT']);
        return $map;
    }
}