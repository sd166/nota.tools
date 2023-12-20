<?php /** @noinspection PhpUnused */

namespace NotaTools\Validators;

use Bitrix\Main\Type\DateTime;
use Rakit\Validation\Rule;

/**
 * Class BitrixDateTimeValidator
 * @package NotaTools\Validators
 */
class BitrixDateTimeValidator extends Rule
{
    /** @var string */
    protected $message = 'Неверный формат даты';

    /**
     * @param $value
     *
     * @return bool
     */
    public function check($value): bool
    {
        if (empty($value) && !is_bool($value)) {
            return true;
        }
        return $value instanceof DateTime || (!is_bool($value) && is_numeric($value) && DateTime::createFromTimestamp((int)$value) instanceof DateTime);
    }
}
