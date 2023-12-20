<?php

namespace NotaTools\Component;

use CBitrixComponent;
use Exception;
use NotaTools\Helpers\LoggerHelper;
use function get_class;

/**
 * Class BaseBitrixComponent
 *
 * Default component for current project
 *
 * @package NotaTools
 */
abstract class BaseBitrixComponent extends CBitrixComponent
{
    /**
     * @var string
     */
    protected $templatePage = '';

    /**
     * @inheritDoc
     */
    public function onPrepareComponentParams($params)
    {
        $params['return_result'] = $params['return_result'] === true || $params['return_result'] === 'Y';
        return parent::onPrepareComponentParams($params);
    }

    /**
     * {@inheritdoc}
     *
     * @return null|array
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            parent::executeComponent();
            $res = $this->beforeCache();
        } catch (Exception $e) {
            LoggerHelper::logComponent($e->getMessage(), get_class($e));
            $this->abortResultCache();
            return null;
        }
        if (!$res) {
            return null;
        }
        if ($this->startResultCache()) {
            try {
                $this->prepareResult();
                $this->includeComponentTemplate($this->templatePage);
                $this->setResultCacheKeys($this->getResultCacheKeys());
            } catch (Exception $e) {
                LoggerHelper::logComponent($e->getMessage(), get_class($e));
                $this->abortResultCache();
                return null;
            }
        }
        try {
            $this->afterTemplate();
        } catch (Exception $e) {
            LoggerHelper::logComponent($e->getMessage(), get_class($e));
            $this->abortResultCache();
            return null;
        }
        if ($this->arParams['return_result']) {
            return $this->arResult;
        }
        return null;
    }

    /**
     * Prepare component result
     */
    abstract public function prepareResult();

    /**
     * @return array
     */
    public function getResultCacheKeys(): array
    {
        return [];
    }

    protected function beforeCache(): bool
    {
        return true;
    }

    protected function afterTemplate(): void
    {
    }

    /**
     * @param string $page
     */
    protected function setTemplatePage(string $page = '')
    {
        $this->templatePage = $page;
    }
}
