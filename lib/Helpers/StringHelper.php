<?php /** @noinspection PhpUnused */

namespace NotaTools\Helpers;

/**
 * Class StringHelper
 * @package NotaTools\Helpers
 */
class StringHelper
{
    /**
     * convert PascalCase to snake_case
     *
     * @param string $input
     *
     * @return string
     */
    public static function convertPascalCaseToSnakeCase(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public static function convertSnakeCaseToDashes(string $input): string
    {
        return str_replace('_', '-', $input);
    }
}
