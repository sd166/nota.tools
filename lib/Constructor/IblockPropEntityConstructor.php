<?php /** @noinspection PhpUnused */

namespace NotaTools\Constructor;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main;

/**
 * Class IblockPropEntityConstructor
 * @package NotaTools\Constructor
 */
class IblockPropEntityConstructor extends EntityConstructor
{
    public const SINGLE_TYPE = 's';
    public const MULTIPLE_TYPE = 'm';

    /**
     * @param int    $iblockId
     *
     * @param string $elementClass
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     */
    public static function getDataClass(int $iblockId, string $elementClass = ElementTable::class)
    {
        return static::getBaseDataClass($iblockId, $elementClass, static::SINGLE_TYPE);
    }

    /**
     * @param string $elementClass
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     */
    public static function geV1DataClass(string $elementClass = ElementTable::class)
    {
        $className = 'ElementPropV1';
        $tableName = 'b_iblock_element_property';
        $additionalFields = [];
        $additionalFields[] = '(new Main\ORM\Fields\Relations\Reference(
                \'ELEMENT\',
                ' . $elementClass . '::getEntity(),
                Main\Entity\Query\Join::on(\'this.IBLOCK_ELEMENT_ID\', \'ref.ID\')
            ))->configureJoinType(\'inner\')';
        $additionalFields[] = '(new Main\ORM\Fields\Relations\Reference(
                \'PROPERTY\',
                \Bitrix\Iblock\PropertyTable::getEntity(),
                Main\Entity\Query\Join::on(\'this.IBLOCK_PROPERTY_ID\', \'ref.ID\')
            ))->configureJoinType(\'inner\')';
        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }

    /**
     * @param int    $iblockId
     *
     * @param string $elementClass
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     */
    public static function getMultipleDataClass(int $iblockId, string $elementClass = ElementTable::class)
    {
        $additionalFields = [];
        $additionalFields[] = '(new Main\ORM\Fields\Relations\Reference(
                \'PROPERTY\',
                \Bitrix\Iblock\PropertyTable::getEntity(),
                Main\Entity\Query\Join::on(\'this.IBLOCK_PROPERTY_ID\', \'ref.ID\')
            ))->configureJoinType(\'inner\')';
        return static::getBaseDataClass($iblockId, $elementClass, static::MULTIPLE_TYPE, $additionalFields);
    }

    /**
     * @param int    $iblockId
     * @param string $elementClass
     * @param string $type
     * @param array  $additionalFields
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     */
    protected static function getBaseDataClass(
        int $iblockId,
        string $elementClass = ElementTable::class,
        string $type = 's',
        array $additionalFields = []
    ) {
        $className = 'ElementProp' . ToUpper($type) . $iblockId;
        $tableName = 'b_iblock_element_prop_' . ToLower($type) . $iblockId;
        $additionalFields[] = '(new Main\ORM\Fields\Relations\Reference(
                \'ELEMENT\',
               ' . $elementClass . '::getEntity(),
                Main\Entity\Query\Join::on(\'this.IBLOCK_ELEMENT_ID\', \'ref.ID\')
            ))->configureJoinType(\'inner\')';
        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }
}