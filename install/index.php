<?php

IncludeModuleLangFile(__FILE__);
use \Bitrix\Main\ModuleManager;

class bx_model extends CModule
{
    public $MODULE_ID = "bx.model";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $errors;

    public function __construct()
    {
        $this->MODULE_VERSION = "1.25.2";
        $this->MODULE_VERSION_DATE = "2021-12-15 14:00:00";
        $this->MODULE_NAME = "Bitrix model";
        $this->MODULE_DESCRIPTION = "";
    }

    /**
     * @return bool
     */
    public function DoInstall(): bool
    {
        ModuleManager::RegisterModule($this->MODULE_ID);
        return true;
    }

    /**
     * @return bool
     */
    public function DoUninstall(): bool
    {
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        return true;
    }

    public function InstallDB()
    {
        return true;
    }

    public function UnInstallDB()
    {
        return true;
    }

    public function InstallEvents()
    {
        return true;
    }

    public function UnInstallEvents()
    {
        return true;
    }

    public function InstallFiles()
    {
        return true;
    }

    public function UnInstallFiles()
    {
        return true;
    }
}
