<?php

namespace NotaTools\Interfaces\Orm\Model;

use Bitrix\Main\IO\FileNotFoundException;
use NotaTools\Exception\Orm\Model\ModelException;

/**
 * Interface ActiveReadModelInterface
 * @package NotaTools\Interfaces\Orm\Model
 */
interface ActiveReadModelInterface
{
    /**
     * ActiveReadModelInterface constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = []);

    /**
     * @param string $primary
     *
     * @return static
     * @throws FileNotFoundException
     * @throws ModelException
     */
    public static function createFromPrimary($primary);
}
