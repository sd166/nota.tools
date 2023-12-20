<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Model;

use NotaTools\Interfaces\Orm\Model\UserBaseInterface;
use NotaTools\Orm\Tables\EO_UserCustomBase;
use NotaTools\Traits\Orm\Model\UserTrait;

/**
 * Class User
 * @package NotaTools\Orm\Model
 */
class UserBase extends EO_UserCustomBase implements UserBaseInterface
{
    use UserTrait;
}