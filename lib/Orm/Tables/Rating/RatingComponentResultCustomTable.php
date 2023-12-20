<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Tables\Rating;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use NotaTools\Constructor\EntityConstructor;

/**
 * Class RatingComponentResultCustomTable
 * @package NotaTools\Orm\Tables\Rating
 */
class RatingComponentResultCustomTable extends DataManager
{

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'b_rating_component_results';
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        /** @todo поставить статичный маппинг - эта таблица неизменна */
        $dataManager = EntityConstructor::compileEntityDataClass('RatingComponentResultCustomBase', static::getTableName());
        return $dataManager::getMap();
    }
}