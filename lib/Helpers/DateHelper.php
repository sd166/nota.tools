<?php /** @noinspection PhpUnused */

namespace NotaTools\Helpers;

use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use DateTime as NormalDateTime;
use Exception;

/**
 * Class DateHelper
 *
 * @package NotaTools\Helpers
 */
class DateHelper
{

    /** именительный падеж */
    public const NOMINATIVE = 'Nominative';

    /** родительный падеж */
    public const GENITIVE = 'Genitive';

    /** именительный падеж короткий*/
    public const SHORT_NOMINATIVE = 'ShortNominative';

    /** родительный падеж короткий */
    public const SHORT_GENITIVE = 'ShortGenitive';

    /** дательный падеж множ. число */
    public const DATIVE_PLURAL = 'DativePlural';

    /**Месяца в родительном падеже*/
    protected static $monthGenitive = [
        '#1#'  => 'Января',
        '#2#'  => 'Февраля',
        '#3#'  => 'Марта',
        '#4#'  => 'Апреля',
        '#5#'  => 'Мая',
        '#6#'  => 'Июня',
        '#7#'  => 'Июля',
        '#8#'  => 'Августа',
        '#9#'  => 'Сентября',
        '#10#' => 'Октября',
        '#11#' => 'Ноября',
        '#12#' => 'Декабря',
    ];

    /** Месяца в именительном падеже  */
    protected static $monthNominative = [
        '#1#'  => 'Январь',
        '#2#'  => 'Февраль',
        '#3#'  => 'Март',
        '#4#'  => 'Апрель',
        '#5#'  => 'Май',
        '#6#'  => 'Июнь',
        '#7#'  => 'Июль',
        '#8#'  => 'Август',
        '#9#'  => 'Сентябрь',
        '#10#' => 'Октябрь',
        '#11#' => 'Ноябрь',
        '#12#' => 'Декабрь',
    ];

    /** кратские месяца в именительном падеже  */
    protected static $monthShortNominative = [
        '#1#'  => 'янв',
        '#2#'  => 'фев',
        '#3#'  => 'мар',
        '#4#'  => 'апр',
        '#5#'  => 'май',
        '#6#'  => 'июн',
        '#7#'  => 'июл',
        '#8#'  => 'авг',
        '#9#'  => 'сен',
        '#10#' => 'окт',
        '#11#' => 'ноя',
        '#12#' => 'дек',
    ];

    /**кратские месяца в родительном падеже*/
    protected static $monthShortGenitive = [
        '#1#'  => 'янв',
        '#2#'  => 'фев',
        '#3#'  => 'мар',
        '#4#'  => 'апр',
        '#5#'  => 'мая',
        '#6#'  => 'июн',
        '#7#'  => 'июл',
        '#8#'  => 'авг',
        '#9#'  => 'сен',
        '#10#' => 'окт',
        '#11#' => 'ноя',
        '#12#' => 'дек',
    ];

    /**дни недели в именительном падеже*/
    protected static $dayOfWeekNominative = [
        '#1#' => 'Понедельник',
        '#2#' => 'Вторник',
        '#3#' => 'Среда',
        '#4#' => 'Четверг',
        '#5#' => 'Пятница',
        '#6#' => 'Суббота',
        '#7#' => 'Воскресенье',
    ];

    /** дни недели в множ. числе дат. падеже */
    protected static $dayOfWeekDativePlural = [
        '#1#' => 'Понедельникам',
        '#2#' => 'Вторникам',
        '#3#' => 'Средам',
        '#4#' => 'Четвергам',
        '#5#' => 'Пятницам',
        '#6#' => 'Субботам',
        '#7#' => 'Воскресеньям',
    ];

    /**краткие дни недели*/
    protected static $dayOfWeekShortNominative = [
        '#1#' => 'пн',
        '#2#' => 'вт',
        '#3#' => 'ср',
        '#4#' => 'чт',
        '#5#' => 'пт',
        '#6#' => 'сб',
        '#7#' => 'вс',
    ];

    /**
     * Подстановка русских месяцев по шаблону
     *
     * @param string $date
     *
     * @param string $case
     *
     * @param bool   $lower
     *
     * @return string
     */
    public static function replaceRuMonth(string $date, string $case = 'Nominative', bool $lower = false): string
    {
        $res = static::replaceStringByArray([
            'date'    => $date,
            'case'    => $case,
            'type'    => 'month',
            'pattern' => '|#\d{1,2}#|',
        ]);
        if ($lower) {
            $res = ToLower($res);
        }
        return $res;
    }

    /**
     * Подстановка дней недели по шаблону
     *
     * @param string $date
     *
     * @param string $case
     *
     * @return string
     */
    public static function replaceRuDayOfWeek(string $date, string $case = 'Nominative'): string
    {
        return static::replaceStringByArray([
            'date'    => $date,
            'case'    => $case,
            'type'    => 'dayOfWeek',
            'pattern' => '|#\d{1}#|',
        ]);
    }

    /**
     * Преобразование битриксового объекта даты в Php
     *
     * @param DateTime $bxDatetime
     *
     * @return NormalDateTime
     * @throws Exception
     */
    public static function convertToDateTime(DateTime $bxDatetime): NormalDateTime
    {
        return (new NormalDateTime())->setTimestamp($bxDatetime->getTimestamp());
    }

    /**
     * Враппер для FormatDate. Доп. возможности
     *  - ll - отображение для недели в винительном падеже (в пятницу, в субботу)
     *  - XX - 'Сегодня', 'Завтра'
     *
     * @param string $dateFormat
     * @param int    $timestamp
     *
     * @return string
     * @throws Exception
     */
    public static function formatDate(string $dateFormat, int $timestamp): string
    {
        $date = (new NormalDateTime)->setTimestamp($timestamp);
        if (false !== mb_strpos($dateFormat, 'll')) {
            $str = null;
            switch ($date->format('w')) {
                case 0:
                    $str = 'в воскресенье';
                    break;
                case 1:
                    $str = 'в понедельник';
                    break;
                case 2:
                    $str = 'во вторник';
                    break;
                case 3:
                    $str = 'в среду';
                    break;
                case 4:
                    $str = 'в четверг';
                    break;
                case 5:
                    $str = 'в пятницу';
                    break;
                case 6:
                    $str = 'в субботу';
                    break;
            }
            if (null !== $str) {
                $dateFormat = str_replace('ll', $str, $dateFormat);
            }
        }
        if (false !== mb_strpos($dateFormat, 'XX')) {
            $tmpDate = clone $date;
            $currentDate = new NormalDateTime();
            $tmpDate->setTime(0, 0, 0);
            $currentDate->setTime(0, 0, 0);
            $diff = $tmpDate->diff($currentDate)->days;
            switch (true) {
                case $diff === 0:
                    $str = 'Сегодня';
                    break;
                case $diff === 1:
                    $str = 'Завтра';
                    break;
                default:
                    $str = 'j F';
            }
            $dateFormat = str_replace('XX', $str, $dateFormat);
        }
        return FormatDate($dateFormat, $timestamp);
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @param array  $setting
     *
     * @return string
     * @throws ObjectException
     */
    public static function getFormattedActiveDate(
        string $dateFrom = '',
        string $dateTo = '',
        array $setting = []
    ): string {
        $result = '';
        if (!isset($setting['with_text'])) {
            $setting['with_text'] = 'с';
        }
        if (!isset($setting['to_text'])) {
            $setting['to_text'] = 'по';
        }
        if (!isset($setting['to_text2'])) {
            $setting['to_text2'] = 'до';
        }
        if (!isset($setting['year_text'])) {
            $setting['year_text'] = 'года';
        }
        $currentDate = new Date();
        if (!empty($dateFrom) && !empty($dateTo)) {
            $result = $setting['with_text'] . ' ';
            $dateFromGen = new Date($dateFrom);
            $dateToGen = new Date($dateTo);
            if ((int)$dateFromGen->format('Y') === (int)$dateToGen->format('Y')) {
                if ((int)$dateFromGen->format('n') === $dateToGen->format('n')) {
                    $result .= $dateFromGen->format('d');
                    $result .= ' ' . $setting['to_text'] . ' ';
                    $result .= static::replaceRuMonth($dateFromGen->format('d #n#'), static::GENITIVE);
                } else {
                    $result .= static::replaceRuMonth($dateFromGen->format('d #n#'), static::GENITIVE);
                    $result .= ' ' . $setting['to_text'] . ' ';
                    $result .= static::replaceRuMonth($dateToGen->format('d #n#'), static::GENITIVE);
                }
                if ((int)$dateFromGen->format('Y') !== $dateFromGen->format('Y')) {
                    $result .= $dateFromGen->format('Y года');
                }
            } else {
                $result .= static::replaceRuMonth($dateFromGen->format('d #n# Y года'), static::GENITIVE);
                $result .= ' ' . $setting['to_text'] . ' ';
                $result .= static::replaceRuMonth($dateToGen->format('d #n# Y года'), static::GENITIVE);
            }
        } elseif (!empty($dateFrom)) {
            $result = $setting['with_text'] . ' ';
            $dateFromGen = new Date($dateFrom);
            if ((int)$dateFromGen->format('Y') === $currentDate->format('Y')) {
                $result .= static::replaceRuMonth($dateFromGen->format('d #n#'), static::GENITIVE);
            } else {
                $result .= static::replaceRuMonth($dateFromGen->format('d #n# Y года'), static::GENITIVE);
            }
        } elseif (!empty($dateTo)) {
            $result = $setting['to_text2'] . ' ';
            $dateToGen = new Date($dateTo);
            if ((int)$dateToGen->format('Y') === $currentDate->format('Y')) {
                $result .= static::replaceRuMonth($dateToGen->format('d #n#'), static::GENITIVE);
            } else {
                $result .= static::replaceRuMonth($dateToGen->format('d #n# Y ' . $setting['year_text']), static::GENITIVE);
            }
        }
        return $result;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected static function replaceStringByArray(array $params): string
    {
        preg_match($params['pattern'], $params['date'], $matches);
        if (!empty($matches[0]) && !empty($params['case'])) {
            $items = static::${$params['type'] . $params['case']};
            if (!empty($items)) {
                return str_replace($matches[0], $items[$matches[0]], $params['date']);
            }
        }
        return $params['date'];
    }
}
