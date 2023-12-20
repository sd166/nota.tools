<?php

namespace NotaTools\Orm\Tables\HL;

use Bitrix\Main;

/**
 * Class BaseHl
 * @package NotaTools\Orm\Tables\HL
 */
abstract class BaseHl extends Main\ORM\Data\DataManager
{
    /**
     * @return string
     */
    abstract public static function getEntityName(): string;

    /**
     * Returns entity map definition.
     *
     * @return array
     * @throws Main\SystemException
     */
    public static function getMap()
    {
        return [
            'ID' => new Main\Entity\IntegerField('ID', [
                'primary'      => true,
                'autocomplete' => true,
            ]),
        ];
    }
}