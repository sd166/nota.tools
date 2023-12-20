<?php /** @noinspection PhpUnused */

namespace NotaTools\Path;

use NotaTools\BitrixUtils;
use NotaTools\Interfaces\Path\TemplateBaseInterface;

/**
 * Class TemplateBase
 * @package NotaTools\Path
 */
class TemplateBase extends TemplateAbstract implements TemplateBaseInterface
{
    /**
     * @return bool
     */
    public function isIndex(): bool
    {
        return $this->isPage('/');
    }

    /**
     * Страница 404
     *
     * @return bool
     */
    public function is404(): bool
    {
        return defined('ERROR_404') && ERROR_404 === BitrixUtils::BX_BOOL_TRUE;
    }

    /**
     * Страница 500
     *
     * @return bool
     */
    public function is500(): bool
    {
        return defined('ERROR_500') && ERROR_500 === BitrixUtils::BX_BOOL_TRUE;
    }

    /**
     * Страница, недоступная для неавторизованных
     *
     * @return bool
     */
    public function isForbidden(): bool
    {
        /**
         * It's bitrix way
         */ global $USER;
        return defined('NEED_AUTH') && NEED_AUTH === true && !$USER->IsAuthorized();
    }
}