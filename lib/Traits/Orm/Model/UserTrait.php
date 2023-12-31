<?php

namespace NotaTools\Traits\Orm\Model;

/**
 * Trait UserTrait
 * @package NotaTools\Traits\Orm\Model
 */
trait UserTrait
{
    /**
     * @return string
     */
    public function getFullName(): string
    {
        $list = [$this->getLastName(), $this->getName(), $this->getSecondName()];
        TrimArr($list, true);
        return implode(' ', $list);
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->getLastName() . ' ' . $this->getNameFirstLetter() . '.';
    }

    /**
     * @return string
     */
    public function getNameFirstLetter(): string
    {
        return mb_substr($this->getName(), 0, 1, LANG_CHARSET);
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return substr($this->getPassword(), -32) ?: '';
    }

    /**
     * @return string
     */
    public function getPasswordSalt(): string
    {
        return substr($this->getPassword(), 0, -32) ?: '';
    }
}