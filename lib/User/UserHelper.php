<?php /** @noinspection PhpUnused */

namespace NotaTools\User;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use NotaTools\BitrixUtils;
use NotaTools\Exception\WrongPhoneNumberException;
use NotaTools\Helpers\PhoneHelper;
use NotaTools\Orm\Tables\UserCustomBaseTable;

/**
 * Class UserHelper
 * @package NotaTools\User
 */
class UserHelper
{
    /**
     * @param string $salt
     * @param string $originalPassword
     *
     * @return string
     */
    public static function getPasswordHash(string $salt, string $originalPassword): string
    {
        return md5($salt . $originalPassword);
    }

    /**
     * @param string $originalPassword
     * @param string $salt
     *
     * @return string
     */
    public static function getPasswordToSave(string $originalPassword, string $salt = ''): string
    {
        if (empty($salt)) {
            $salt = static::getPasswordSalt();
        }
        return $salt . static::getPasswordHash($salt, $originalPassword);
    }

    /**
     * @return string
     */
    public static function getPasswordSalt(): string
    {
        return randString(8, [
            'abcdefghijklnmopqrstuvwxyz',
            'ABCDEFGHIJKLNMOPQRSTUVWXYZ',
            '0123456789',
            ",.<>/?;:[]{}\\|~!@#\$%^&*()-_+=",
        ]);
    }

    /**
     * @param string             $email
     * @param ConditionTree|null $additionalFilter
     * @param bool               $active
     *
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getCountUsersByEmail(
        string $email,
        bool $active = true,
        ?ConditionTree $additionalFilter = null
    ): int {
        $condition = new ConditionTree();
        $condition->where('EMAIL', $email);
        if ($active) {
            $condition->where('ACTIVE', BitrixUtils::BX_BOOL_TRUE);
        }
        if ($additionalFilter !== null) {
            $condition->where($additionalFilter);
        }
        return UserCustomBaseTable::getCount($condition);
    }

    /**
     * @param string             $phone
     * @param ConditionTree|null $additionalFilter
     * @param bool               $active
     *
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws WrongPhoneNumberException
     */
    public static function getCountUsersByPhone(
        string $phone,
        bool $active = true,
        ?ConditionTree $additionalFilter = null
    ): int {
        $condition = new ConditionTree();
        $condition->where('PERSONAL_PHONE', PhoneHelper::normalizePhone($phone));
        if ($active) {
            $condition->where('ACTIVE', BitrixUtils::BX_BOOL_TRUE);
        }
        if ($additionalFilter !== null) {
            $condition->where($additionalFilter);
        }
        return UserCustomBaseTable::getCount($condition);
    }
}