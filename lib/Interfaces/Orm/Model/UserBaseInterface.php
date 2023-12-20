<?php

namespace NotaTools\Interfaces\Orm\Model;

/**
 * Class User
 * @package NotaTools\Orm\Model
 */
interface UserBaseInterface
{
    /**
     * @return string
     */
    public function getFullName(): string;

    /**
     * @return string
     */
    public function getShortName(): string;

    /**
     * @return string
     */
    public function getNameFirstLetter(): string;

    /**
     * @return string
     */
    public function getPasswordHash(): string;

    /**
     * @return string
     */
    public function getPasswordSalt(): string;
}