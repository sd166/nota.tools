<?php

namespace NotaTools\Interfaces\Orm\Model;

/**
 * Interface FileInterface
 *
 * @package NotaTools\Interfaces\Orm\Model
 */
interface FileInterface extends ActiveReadModelInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getSrc(): string;

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return array
     */
    public function getFields(): array;

    /**
     * @return string
     */
    public function getFileName(): string;

    /**
     * @return string
     */
    public function getSubDir(): string;
}
