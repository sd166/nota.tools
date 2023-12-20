<?php /** @noinspection PhpUnused */

namespace NotaTools\User;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CUser;
use NotaTools\Orm\Model\UserBase;
use NotaTools\Orm\Tables\UserCustomBaseTable;

/**
 * Class User
 * @package NotaTools\User
 */
class User extends CUser
{
    /**
     * @param $login
     * @param $password
     *
     * @return bool
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public static function checkUserPassword($login, $password): bool
    {
        $query = UserCustomBaseTable::query();
        $query->setSelect(['ID', 'PASSWORD'])->where('LOGIN', $login);
        /** @var UserBase $user */
        $user = $query->exec()->fetchObject();
        if ($user === null) {
            return false;
        }
        return UserHelper::getPasswordHash($user->getPasswordSalt(), $password) === $user->getPasswordHash();
    }
}