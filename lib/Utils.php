<?php /** @noinspection PhpUnused */

namespace NotaTools;

use Bitrix\Main\Type\DateTime;
use function is_array;
use function is_object;

/**
 * Class Utils
 * @package NotaTools
 *
 * Все прочие полезные функции, для которых пока нет отдельного класса.
 */
class Utils
{
    /**
     * Возвращает имя класса без namespace
     *
     * @param $object
     *
     * @return string
     */
    public static function getClassName($object): string
    {
        $className = get_class($object);
        $pos = strrpos($className, '\\');
        if ($pos) {

            return substr($className, $pos + 1);
        }
        return $pos;
    }

    /**
     * @param array $list
     * @param array $excludeKeys
     */
    public static function eraseArray(&$list, array $excludeKeys = []): void
    {
        $tmpList = [];
        foreach ($list as $key => $val) {
            $tmpList[$key] = $val;
        }
        $list = static::eraseArrayReturn($tmpList, $excludeKeys);
    }

    /**
     * @param array $params
     *
     * @return array|bool|mixed
     */
    public static function getUniqueArray(array $params = [])
    {
        if (!isset($params['arr1'])) {
            return false;
        }
        if (!isset($params['arr2'])) {
            return $params['arr1'];
        }
        if (!isset($params['bReturnFullDiffArray'])) {
            $params['bReturnFullDiffArray'] = false;
        }
        if (!isset($params['isChild'])) {
            $params['isChild'] = false;
        }
        if (!isset($params['skipKeys'])) {
            $params['skipKeys'] = [];
        }
        $result = [];
        if ($params['bReturnFullDiffArray'] && $params['isChild']) {
            $tmpList = [];
            $diff = [];
        }
        foreach ($params['arr1'] as $key => $val) {
            if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                $tmpList[$key] = $val;
            }
            if (is_array($val)) {
                if (!in_array($key, $params['skipKeys'], true)) {
                    if (!isset($params['arr2'][$key]) || (!empty($val) && empty($params['arr2'][$key]))) {
                        if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                            $diff[$key] = $val;
                        } else {
                            $result[$key] = $val;
                        }
                    } else {
                        $return = self::getUniqueArray([
                            'arr1'                 => $val,
                            'arr2'                 => $params['arr2'][$key],
                            'bReturnFullDiffArray' => $params['bReturnFullDiffArray'],
                            'skipKeys'             => $params['skipKeys'],
                            'isChild'              => true,
                        ]);
                        if (!empty($return)) {
                            if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                                $diff[$key] = $return;
                            } else {
                                $result[$key] = $return;
                            }
                        }
                    }
                }
            } else {
                if (!in_array($key, $params['skipKeys'], true)) {
                    if (!isset($params['arr2'][$key])) {
                        if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                            $diff[$key] = $val;
                        } else {
                            $result[$key] = $val;
                        }
                    } else {
                        $tmpVal = '0';
                        $tmpArr2Val = '1';
                        if (is_object($val)) {
                            if ($val instanceof DateTime) {
                                /** @var DateTime $val */
                                $tmpVal = $val->format(DateTime::getFormat());
                                /** @var DateTime $val2 */
                                $val2 = $params['arr2'][$key];
                                $tmpArr2Val = $val2->format(DateTime::getFormat());
                                unset($val2);
                            }
                        }
                        if ((is_object($val) && $tmpVal !== $tmpArr2Val) || (!is_object($val) && $val !== $params['arr2'][$key])) {
                            if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                                $diff[$key] = $val;
                            } else {
                                $result[$key] = $val;
                            }
                        }
                    }
                }
            }
        }
        if ($tmpList !== null && !empty($tmpList) && $diff !== null && count($diff) > 0) {
            $result = $tmpList;
        }
        return $result;
    }

    /**
     * @param array $list
     *
     * @param array $excludeKeys
     *
     * @return array
     */
    protected static function eraseArrayReturn($list, array $excludeKeys = []): array
    {
        foreach ($list as $key => $val) {
            if (is_object($val) || in_array($key, $excludeKeys, true)) {
                continue;
            }
            if (is_array($val)) {
                if (!empty($val)) {
                    $newVal = self::eraseArrayReturn($val);
                    if ($newVal === null || empty($newVal)) {
                        unset($list[$key]);
                    } else {
                        $list[$key] = $val = $newVal;
                    }
                } else {
                    unset($list[$key]);
                }
            } else {
                if ($val === null || empty($val)) {
                    unset($list[$key]);
                }
            }
        }
        return $list;
    }
}
