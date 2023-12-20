<?php

namespace NotaTools\Interfaces\Path;

use Bitrix\Main\Context;
use Bitrix\Main\Context\Culture;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Response;
use Bitrix\Main\Server;
use Bitrix\Main\Web\Uri;

/**
 * Class TemplateAbstractInterface
 *
 * Класс для управления условиями в шаблонах.
 *
 * Определяется три типа методов:
 *
 * - is... : определяет атомарное условие или группу условий (например, isIndex())
 * - has... : композиция условий типа is..., определяет наличие блока в шаблоне. Не должен содержать никакой логики,
 *            помимо вызова методов is и условных операторов
 * - get... : получение чего-либо, используемого в шаблоне.
 *
 * @package NotaTools\Interfaces\Path
 */
interface TemplateAbstractInterface
{
    /**
     * @param Context $context
     *
     * @return static
     */
    public static function getInstance(Context $context): TemplateAbstractInterface;

    /**
     * Находимся на странице $page
     *
     * @param string $page
     *
     * @return bool
     */
    public function isPage($page): bool;

    /**
     * @param $src
     *
     * @return bool
     */
    public function isPartitionPage($src): bool;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @return bool
     */
    public function isAjaxRequest(): bool;

    /**
     * @return HttpRequest
     */
    public function getRequest(): HttpRequest;

    /**
     * @return Uri
     */
    public function getUri(): Uri;

    /**
     * @return Server
     */
    public function getServer(): Server;

    /**
     * @return Culture
     */
    public function getCulture(): Culture;

    /**
     * @return Response
     */
    public function getResponse(): Response;

    /**
     * @return bool|string|null
     */
    public function getDir();

    /**
     * @param $src
     *
     * @return bool
     */
    public function isPartitionDir($src): bool;

    /**
     * @param $dir
     *
     * @return bool
     */
    public function isDir($dir): bool;

    /**
     * @param $src
     *
     * @return bool
     */
    public function isPartitionDirByFilePath($src): bool;
}
