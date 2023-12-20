<?php

namespace NotaTools\Iblock;

use Bitrix\Iblock\EO_SectionElement_Result;
use Bitrix\Iblock\SectionElementTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\SystemException;
use NotaTools\BitrixUtils;

/**
 * Class ElementHelper
 * @package NotaTools\Iblock
 */
class ElementHelper
{
    /**
     * @param array $params
     *
     * @return EO_SectionElement_Result|Result
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getSectionElementBySectRecursive(
        array $params
    ) {
        if ((int)$params['IBLOCK_ID'] <= 0) {
            throw new ArgumentException('не задан id инфоблока');
        }
        $iblockId = (int)$params['IBLOCK_ID'];
        if (!is_object($params['SECTION']) || $params['SECTION']->getLeftMargin() === null) {
            throw new ArgumentException('не задан раздел');
        }
        $section = $params['SECTION'];
        $includeCurrent = $params['INCLUDE_CURRENT'] === true;
        $onlyActive = $params['ONLY_ACTIVE'] === true;
        $limit = (int)$params['LIMIT'];
        if ($limit < 0) {
            $limit = 0;
        }
        if (!is_array($params['SELECT']) || empty($params['SELECT'])) {
            $select = ['IBLOCK_ELEMENT_ID', 'IBLOCK_SECTION_ID'];
        } else {
            $select = $params['SELECT'];
        }
        $query = SectionElementTable::query();
        if ($limit > 0) {
            $query->setLimit($limit);
        }
        $query->setSelect($select);
        $conditionTree = new ConditionTree();
        if ($params['CHILD_SECTIONS']) {
            $conditionTree->whereIn('IBLOCK_SECTION.ID', $params['CHILD_SECTIONS']);
        }
        if ($params['ELEMENTS']) {
            $conditionTree->whereIn('IBLOCK_ELEMENT.ID', $params['ELEMENTS']);
        }
        $conditionTree->where('IBLOCK_SECTION.IBLOCK_ID', $iblockId)->where('IBLOCK_ELEMENT.IBLOCK_ID', $iblockId);
        if ($onlyActive) {
            $conditionTree->where('IBLOCK_SECTION.ACTIVE', BitrixUtils::BX_BOOL_TRUE)->where('IBLOCK_ELEMENT.ACTIVE', BitrixUtils::BX_BOOL_TRUE);
        }
        if ($includeCurrent) {
            $conditionTree->where('IBLOCK_SECTION.LEFT_MARGIN', '>=', $section->getLeftMargin())
                ->where('IBLOCK_SECTION.RIGHT_MARGIN', '<=', $section->getRightMargin());
        } else {
            $conditionTree->where('IBLOCK_SECTION.LEFT_MARGIN', '>', $section->getLeftMargin())
                ->where('IBLOCK_SECTION.RIGHT_MARGIN', '<', $section->getRightMargin());
        }
        $query->where($conditionTree);
        return $query->exec();
    }
}
