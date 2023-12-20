<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables\Rating;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\SystemException;
use NotaTools\Constructor\EntityConstructor;
use Sevensuns\Utils\Orm\Tables\UserCustomTable;

/**
 * Class RatingVoteCustomTable
 * @package NotaTools\Orm\Tables\Rating
 */
class RatingVoteCustomTable extends DataManager
{

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'b_rating_vote';
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        $dataManager = EntityConstructor::compileEntityDataClass('RatingVoteCustomTable', static::getTableName());
        $map = $dataManager::getMap();
        $map['USER'] = new Reference('USER', UserCustomTable::class, ['=this.USER_ID' => 'ref.ID'], ['join_type' => 'LEFT']);
        return $map;
    }
}