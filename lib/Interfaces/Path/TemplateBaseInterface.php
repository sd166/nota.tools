<?php namespace NotaTools\Interfaces\Path;

/**
 * Class MainTemplate
 *
 * Класс для основного шаблона
 *
 * @package NotaTools\Interfaces\Path
 */
interface TemplateBaseInterface extends TemplateAbstractInterface
{
    /**
     * @return bool
     */
    public function isIndex(): bool;

    /**
     * Страница 404
     *
     * @return bool
     */
    public function is404(): bool;

    /**
     * Страница, недоступная для неавторизованных
     *
     * @return bool
     */
    public function isForbidden(): bool;
}
