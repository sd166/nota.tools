<?php /** @noinspection PhpUnused */

namespace NotaTools\User;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\EO_Group;
use Bitrix\Main\GroupTable;
use Bitrix\Main\SystemException;
use NotaTools\Enum\CacheTimeEnum;
use NotaTools\Exception\User\UserGroupNotFoundException;
use NotaTools\Helpers\TaggedCacheHelper;

/**
 * Class UserGroupHelper
 * @package NotaTools\User
 */
class UserGroupHelper
{
    public const BASE_SELECT = ['ID', 'ACTIVE', 'C_SORT', 'IS_SYSTEM', 'ANONYMOUS', 'NAME', 'DESCRIPTION', 'STRING_ID'];
    /**
     * @var array
     */
    protected static $groups = [];

    /**
     * @param string $code
     *
     * @return EO_Group
     * @throws ArgumentException
     * @throws UserGroupNotFoundException
     * @throws SystemException
     */
    public static function getGroupByCode(string $code): EO_Group
    {
        if (empty($code)) {
            throw new ArgumentException('не задан код');
        }
        if (empty(static::$groups)) {
            static::loadGroups();
        }
        if (!isset(static::$groups[$code])) {
            throw new UserGroupNotFoundException();
        }
        return static::$groups[$code];
    }

    /**
     * @param string $code
     *
     * @return int
     * @throws ArgumentException
     * @throws SystemException
     * @throws UserGroupNotFoundException
     */
    public static function getGroupIdByCode(string $code): int
    {
        return static::getGroupByCode($code)->getId();
    }

    protected static function loadGroups(): void
    {
        $cache = Application::getInstance()->getCache();
        $cacheDir = '/user/group_helper';
        $cacheUniqueString = 'user_group_helper';
        $cacheTag = 'user_group_helper';
        $actualGroups = [];
        if ($cache->initCache(CacheTimeEnum::MONTH, $cacheUniqueString, $cacheDir)) {
            $vars = $cache->getVars();
            $actualGroupsData = $vars['RES'];
            if (!empty($actualGroupsData)) {
                foreach ($actualGroupsData as $item) {
                    $obj = EO_Group::wakeUp($item);
                    $actualGroups[$obj->getStringId()] = $obj;
                }
                unset($obj);
            }
            unset($actualGroupsData);
        } elseif ($cache->startDataCache()) {
            $tagCache = new TaggedCacheHelper($cacheDir);
            /** @var EO_Group $group */
            $query = GroupTable::query();
            $groups = $query->setSelect(static::BASE_SELECT)
                ->exec()
                ->fetchCollection();
            foreach ($groups as $group) {
                $actualGroups[$group->getStringId()] = $group;
            }
            $actualGroupsData = [];
            foreach ($actualGroups as $obj) {
                /** @var EO_Group $obj */
                $item = $obj->collectValues();
                if (isset($item['UTS_OBJECT'])) {
                    unset($item['UTS_OBJECT']);
                }
                $actualGroupsData[] = $item;
            }
            $tagCache->addTag($cacheTag);
            $tagCache->end();
            $cache->endDataCache(['RES' => $actualGroupsData]);
        }
        static::$groups = $actualGroups;
    }
}