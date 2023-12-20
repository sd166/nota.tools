<?php

namespace NotaTools\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;

/**
 * Class FilterHelper
 * @package NotaTools\Helpers
 */
class FilterHelper
{
    /**
     * @param string        $code
     * @param               $value
     *
     * @param ConditionTree $condition
     *
     * @throws ArgumentException
     */
    public static function setFilterVal(string $code, $value, ConditionTree $condition): void
    {
        if (!empty($code)) {
            $isNot = false;
            if (strpos($code, '!') !== false) {
                $isNot = true;
                $code = str_replace('!', '', $code);
            }
            if (is_array($value) && !empty($value)) {
                if ($isNot) {
                    $condition->whereNotIn($code, $value);
                } else {
                    $condition->whereIn($code, $value);
                }
            } else {
                if (is_string($value) || is_numeric($value)) {
                    $operation = '=';
                    if (strpos($code, '>=') === 0) {
                        $code = str_replace('>=', '', $code);
                        $operation = '>=';
                    } elseif (strpos($code, '<=') === 0) {
                        $code = str_replace('<=', '', $code);
                        $operation = '<=';
                    } elseif (strpos($code, '>') === 0) {
                        $code = ltrim($code, '>');
                        $operation = '>';
                    } elseif (strpos($code, '<') === 0) {
                        $code = ltrim($code, '<');
                        $operation = '<';
                    }
                    if ($isNot) {
                        $condition->whereNot($code, $operation, $value);
                    } else {
                        $condition->where($code, $operation, $value);
                    }
                } else if ($value === null) {
                    if ($isNot) {
                        $condition->whereNotNull($code);
                    } else {
                        $condition->whereNull($code);
                    }
                }
            }
        }
    }
}