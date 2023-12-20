<?php

use Bitrix\Main\ModuleManager;
use NotaTools\Events\IblockCacheHelpersEvents;
use NotaTools\Events\UserCacheHelpersEvents;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
if (class_exists('nota_tools')) {
    return;
}

class nota_tools extends CModule
{
    /** @var string */
    public $MODULE_ID;

    /** @var string */
    public $MODULE_VERSION;

    /** @var string */
    public $MODULE_VERSION_DATE;

    /** @var string */
    public $MODULE_NAME;

    /** @var string */
    public $MODULE_DESCRIPTION;

    /** @var string */
    public $MODULE_GROUP_RIGHTS;

    /** @var string */
    public $PARTNER_NAME;

    /** @var string */
    public $PARTNER_URI;

    public function __construct()
    {
        $this->MODULE_ID = 'nota.tools';
        $this->MODULE_VERSION = '0.0.1';
        $this->MODULE_VERSION_DATE = '2019-08-22 15:37:14';
        $this->MODULE_NAME = 'Модуль с расширением функционал';
        $this->MODULE_DESCRIPTION = 'Модуль содержит вспомогательные классы и модели';
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = 'Nota.Media';
        $this->PARTNER_URI = 'http://nota.media';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        IblockCacheHelpersEvents::bind();
        UserCacheHelpersEvents::bind();
    }

    public function doUninstall()
    {
        IblockCacheHelpersEvents::unBind();
        UserCacheHelpersEvents::unBind();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }
}