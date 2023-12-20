<?php /** @noinspection PhpUnused */

namespace NotaTools\Validators;

use NotaTools\Helpers\PhoneHelper;
use Rakit\Validation\Rule;

/**
 * Class PhoneValidator
 * @package NotaTools\Validators
 */
class PhoneValidator extends Rule
{
    /** @var string */
    protected $message = 'Неверный формат номера телефона';

    /**
     * @param $value
     *
     * @return bool
     */
    public function check($value): bool
    {
        if (empty($value)) {
            return true;
        }
        return PhoneHelper::isPhone($value);
    }
}
