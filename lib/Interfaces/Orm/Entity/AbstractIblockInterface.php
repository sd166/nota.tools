<?php /** @noinspection PhpUnused */

namespace NotaTools\Interfaces\Orm\Entity;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\DateTime;
use NotaTools\Exception\Iblock\IblockException;
use NotaTools\Exception\Iblock\IblockNotFoundException;
use NotaTools\Exception\Iblock\IblockPropertyNotFoundException;
use NotaTools\Exception\Iblock\PropertyEnumNotFoundException;
use NotaTools\Orm\Tables\Iblock\EO_ElementCustom;
use Sevensuns\Utils\Orm\Model\UserCustom;

/**
 * Class AbstractIblockInterface
 * @package NotaTools\Interfaces\Orm\Entity
 * @method AbstractIblockInterface|static set(string $code, mixed $value)
 * @method AbstractIblockInterface|static add(string $code, mixed $value)
 * @method mixed get(string $code, bool $formatted = false)
 */
interface AbstractIblockInterface
{
    public const FORMATTED_TYPE = [
        'ACTUAL'         => 0,
        'REAL'           => 1,
        'FORMATTED'      => 2,
        'FORMATTED_EDIT' => 3,
        'FORMATTED_LIST' => 4,
    ];
    public const NO_VAL = 'Нет';

    /**
     * @return array
     */
    public function getSingleProps(): array;

    /**
     * @return array
     */
    public function getMultipleProps(): array;

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return UserCustom|null
     */
    public function getCreatedBy(): ?UserCustom;

    /**
     * @return int|null
     */
    public function getCreatedByReal(): ?int;

    /**
     * @return DateTime
     */
    public function getDateCreate(): DateTime;

    /**
     * @return int
     */
    public function getDateCreateReal(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return static|AbstractIblockInterface
     */
    public function setName(string $name): AbstractIblockInterface;

    /**
     * @return EO_ElementCustom|mixed
     */
    public function getElement();

    /**
     * @param array $data
     *
     * @return static|AbstractIblockInterface
     */
    public function setData(array $data): AbstractIblockInterface;

    /**
     * @param $code
     *
     * @return bool
     */
    public function hasFieldRead($code): bool;

    /**
     * @param bool $withMultipleCurrents
     *
     * @return array
     */
    public function getFieldsWrite(bool $withMultipleCurrents = false): array;

    /**
     * @param bool $withMultipleCurrents
     *
     * @return array
     */
    public function getFieldsRead(bool $withMultipleCurrents = false): array;

    /**
     * @return array
     */
    public function getFieldsMultiple(): array;

    /**
     * @return array
     */
    public function getFieldsMultipleWithCurrent(): array;

    /**
     * @param $code
     *
     * @return bool
     */
    public function hasFieldWrite($code): bool;

    /**
     * @param      $code
     * @param bool $withCurrent
     *
     * @return bool
     */
    public function hasFieldMultiple($code, $withCurrent = false): bool;

    /**
     * @param int $formatted
     *
     * @return array
     * @throws ArgumentException
     * @throws IblockException
     * @throws IblockNotFoundException
     * @throws IblockPropertyNotFoundException
     * @throws ObjectException
     * @throws PropertyEnumNotFoundException
     */
    public function toArray(
        int $formatted = self::FORMATTED_TYPE['ACTUAL']
    ): array;
}