<?php /** @noinspection PhpUnused */

namespace NotaTools;

use Bitrix\Main\Result;

/**
 * Class BitrixUtils
 * @package NotaTools
 *
 * Все прочие полезные функции зависимые от Битрикс, для которых пока нет отдельного класса.
 */
class BitrixUtils
{
    public const BX_BOOL_FALSE = 'N';
    public const BX_BOOL_TRUE = 'Y';

    /**
     * Определяет является ли запрос аяксовым
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' || $_SERVER['HTTP_BX_AJAX'] === 'true');
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    public static function bool2BitrixBool($value): string
    {
        return $value ? self::BX_BOOL_TRUE : self::BX_BOOL_FALSE;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function bitrixBool2bool($value): bool
    {
        return self::BX_BOOL_TRUE === $value;
    }

    /**
     * Возвращает одно сообщение об ошибке из любого Битриксового результата
     *
     * @param Result $result
     *
     * @return string
     */
    public static function extractErrorMessage(Result $result): string
    {
        return implode('; ', $result->getErrorMessages());
    }
}
