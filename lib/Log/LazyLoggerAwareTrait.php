<?php /** @noinspection PhpUnused */

namespace NotaTools\Log;

use Exception;
use NotaTools\Utils;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Trait LazyLoggerAwareTrait
 *
 * Позволяет сразу использовать метод log() для логирования без необходимости однообразно вызывать фабрику создания
 * логеров LoggerFactory. Если требуется в некоторых случаях изменить тип лога или его имя, то перед первым вызовом
 * log() или в конструкторе следует установить соответствующие параметры. Дополнительный плюс - вместо прямой работы со
 * свойством logger идёт работа с методом log() , благодаря которой и код выглядит более читаемо ( log()->error('!') ),
 * и метод log() можно в любом частном случае переопределить под конкретные нужды.
 *
 * @package NotaTools\Log
 *
 */
trait LazyLoggerAwareTrait
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    protected $logName;

    /**
     * @var string
     */
    protected $logType = 'main';

    /**
     * @return LoggerInterface
     * @throws Exception
     */
    public function log(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = LoggerFactory::create($this->getLogName(), $this->getLogType());
        }
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getLogType(): string
    {
        return $this->logType;
    }

    /**
     * @param string $logType
     *
     * @return $this
     */
    public function withLogType(string $logType): self
    {
        $this->logType = $logType;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogName(): string
    {
        if ($this->logName === null) {
            $this->logName = Utils::getClassName($this);
        }
        return $this->logName;
    }

    /**
     * @param string $logName
     *
     * @return $this
     */
    public function withLogName(string $logName): self
    {
        $this->logName = $logName;
        return $this;
    }

}
