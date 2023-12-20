<?php /** @noinspection PhpUnused */

namespace NotaTools\Iblock;

use Bitrix\Iblock\EO_Section;
use Bitrix\Iblock\EO_Section_Collection;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\FieldTypeMask;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use NotaTools\BitrixUtils;
use NotaTools\Exception\Iblock\SectionNotFoundException;
use Sevensuns\Utils\Orm\Tables\Iblock\EO_Scope;
use Sevensuns\Utils\Orm\Tables\Iblock\EO_WorkScope;

/**
 * Class SectionHelper
 * @package NotaTools\Iblock
 */
class SectionHelper
{
    /**
     * @param       $currentSect
     * @param array $select
     *
     * @return EO_Section
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getRootSectionByObject(EO_Section $currentSect, array $select = ['*']): ?EO_Section
    {
        if ($currentSect->getDepthLevel() === null) {
            $currentSect->fillDepthLevel();
        }
        if ($currentSect->getDepthLevel() === 1) {
            if ($select[0] === '**') {
                $fillFields = FieldTypeMask::ALL;
            } elseif ($select[0] === '*') {
                $fillFields = FieldTypeMask::SCALAR;
            } else {
                $fillFields = [];
                foreach ($select as $val) {
                    if ($currentSect->get($val) === null) {
                        $fillFields[] = $val;
                    }
                }
            }
            if (!empty($fillFields)) {
                $currentSect->fill($fillFields);
            }
            return $currentSect;
        }
        /** @var EO_Section $rootSection */
        $query = SectionTable::query();
        $query->where((new ConditionTree())->where('IBLOCK_ID', $currentSect->getIblockId())
            ->where('DEPTH_LEVEL', 1)
            ->where('LEFT_MARGIN', '<=', $currentSect->getLeftMargin())
            ->where('RIGHT_MARGIN', '>=', $currentSect->getRightMargin()));
        return $query->setSelect($select)->setLimit(1)->exec()->fetchObject();
    }

    /**
     * @param int   $id
     * @param array $select
     *
     * @return EO_Section
     * @throws ArgumentException
     * @throws SectionNotFoundException
     * @throws SystemException
     */
    public static function getRootSectionById(int $id, array $select = ['*']): EO_Section
    {
        if ($id <= 0) {
            throw new ArgumentException('Неверный Id');
        }
        $currentSect = SectionTable::getById($id)->fetchObject();
        if ($currentSect === null) {
            throw new SectionNotFoundException();
        }
        return static::getRootSectionByObject($currentSect, $select);
    }

    /**
     * @param array $data
     * @param array $select
     *
     * @return EO_Section
     * @throws ArgumentException
     * @throws SectionNotFoundException
     * @throws SystemException
     */
    public static function getRootSectionByArray(array $data, array $select = ['*']): EO_Section
    {
        if (empty($data['ID']) || (int)$data['ID'] <= 0) {
            throw new ArgumentException('Нет ID');
        }
        if (!empty($data['LEFT_MARGIN']) && !empty($data['RIGHT_MARGIN']) && !empty($data['IBLOCK_ID'])) {
            $currentSect = EO_Section::wakeUp([
                'ID'           => (int)$data['ID'],
                'IBLOCK_ID'    => (int)$data['IBLOCK_ID'],
                'LEFT_MARGIN'  => (int)$data['LEFT_MARGIN'],
                'RIGHT_MARGIN' => (int)$data['RIGHT_MARGIN'],
            ]);
            return static::getRootSectionByObject($currentSect, $select);
        }
        return static::getRootSectionById((int)$data['ID'], $select);
    }

    /**
     * @param EO_Section|EO_Scope|EO_WorkScope $currentSect
     * @param bool                             $includeCurrent
     * @param array                            $select
     * @param DataManager|string|null          $class
     *
     * @return EO_Section_Collection|mixed
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public static function getPathByCurrentSect(
        $currentSect,
        bool $includeCurrent = false,
        array $select = ['*'],
        $class = null
    ) {
        if ($class === null) {
            $class = SectionTable::class;
        }
        $query = $class::query();
        $conditions = new ConditionTree();
        $conditions->where('ACTIVE', BitrixUtils::BX_BOOL_TRUE);
        $conditions->where('IBLOCK_ID', $currentSect->getIblockId())
            ->where('LEFT_MARGIN', '<=', $currentSect->getLeftMargin())
            ->where('RIGHT_MARGIN', '>=', $currentSect->getRightMargin());
        $currentDepthLvl = $currentSect->getDepthLevel();
        if ($includeCurrent) {
            if ($currentDepthLvl > 1) {
                $conditions->whereBetween('DEPTH_LEVEL', 1, $currentDepthLvl);
            } else {
                $conditions->where('DEPTH_LEVEL', 1);
            }
        } else {
            if ($currentDepthLvl > 2) {
                $conditions->whereBetween('DEPTH_LEVEL', 1, $currentDepthLvl - 1);
            } else {
                if ($currentDepthLvl === 1) {
                    return $class::createCollection();
                }
                $conditions->where('DEPTH_LEVEL', 1);
            }
        }
        $query->where($conditions);
        return $query->setSelect($select)->setOrder(['LEFT_MARGIN' => 'ASC'])->exec()->fetchCollection();
    }

    /**
     * @param int                     $id
     * @param bool                    $includeCurrent
     * @param array                   $select
     *
     * @param DataManager|string|null $class
     *
     * @return EO_Section_Collection|mixed
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SectionNotFoundException
     * @throws SystemException
     */
    public static function getPathById(
        int $id,
        bool $includeCurrent = false,
        array $select = ['*'],
        $class = null
    ) {
        if ($id <= 0) {
            throw new ArgumentException('Неверный Id');
        }
        if ($class === null) {
            $class = SectionTable::class;
        }
        $currentSect = $class::getById($id)->fetchObject();
        if ($currentSect === null) {
            throw new SectionNotFoundException();
        }
        return static::getPathByCurrentSect($currentSect, $includeCurrent, $select, $class);
    }

    /**
     * @param int                     $id
     * @param bool                    $includeCurrent
     * @param array                   $select
     * @param DataManager|string|null $class
     *
     * @return EO_Section_Collection|mixed
     * @throws ArgumentException
     * @throws SectionNotFoundException
     * @throws SystemException
     */
    public static function getChildrenSectionsById(
        int $id,
        bool $includeCurrent = false,
        array $select = ['*'],
        $class = null
    ) {
        if ($id <= 0) {
            throw new ArgumentException('Неверный Id');
        }
        if ($class === null) {
            $class = SectionTable::class;
        }
        $currentSect = $class::getById($id)->fetchObject();
        if ($currentSect === null) {
            throw new SectionNotFoundException();
        }
        return static::getChildrenSectionsByCurrentSect($currentSect, $includeCurrent, $select, $class);
    }

    /**
     * @param EO_Section              $currentSect
     * @param bool                    $includeCurrent
     * @param array                   $select
     * @param DataManager|string|null $class
     *
     * @return EO_Section_Collection|mixed
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getChildrenSectionsByCurrentSect(
        $currentSect,
        bool $includeCurrent = false,
        array $select = ['*'],
        $class = null
    ) {
        if ($class === null) {
            $class = SectionTable::class;
        }
        $query = $class::query();
        $conditions = new ConditionTree();
        $conditions->where('ACTIVE', BitrixUtils::BX_BOOL_TRUE);
        $conditions->where('IBLOCK_ID', $currentSect->getIblockId());
        if ($includeCurrent) {
            $conditions->where('LEFT_MARGIN', '>=', $currentSect->getLeftMargin())
                ->where('RIGHT_MARGIN', '<=', $currentSect->getRightMargin());
        } else {
            $conditions->where('LEFT_MARGIN', '>', $currentSect->getLeftMargin())
                ->where('RIGHT_MARGIN', '<', $currentSect->getRightMargin());
        }
        $query->where($conditions);
        $collection = $query->setSelect($select)->setOrder(['LEFT_MARGIN' => 'ASC'])->exec()->fetchCollection();
        if ($collection === null) {
            $collection = $class::createCollection();
        }
        return $collection;
    }
}