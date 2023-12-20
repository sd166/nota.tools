<?php

namespace NotaTools\Orm\Entity;

use Bitrix\Iblock\EO_PropertyEnumeration;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Db\SqlQueryException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\FieldTypeMask;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Objectify\Values;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Exception;
use NotaTools\BitrixUtils;
use NotaTools\Exception\Iblock\ElementNotFoundException;
use NotaTools\Exception\Iblock\IblockException;
use NotaTools\Exception\Iblock\IblockNotFoundException;
use NotaTools\Exception\Iblock\IblockPropertyNotFoundException;
use NotaTools\Iblock\PropertyHelper;
use NotaTools\Interfaces\Orm\Entity\AbstractIblockInterface;
use NotaTools\Orm\Model\Image;
use NotaTools\Orm\Tables\EO_UserCustomBase;
use NotaTools\Orm\Tables\Iblock\EO_ElementCustom;
use Sevensuns\Utils\Orm\Model\UserCustom;

/**
 * Class AbstractIblock
 * @package NotaTools\Orm\Entity
 */
abstract class AbstractIblock implements AbstractIblockInterface
{
    protected const EVENT_MODULE_ID = 'nota.tools';
    protected const FIELDS_READ_BASE = [
        'ID',
        'DATE_CREATE',
        'NAME',
    ];
    protected const FIELDS_READ = [];
    protected const FIELDS_WRITE_BASE = [
        'NAME',
    ];
    protected const FIELDS_WRITE = [];
    protected const PROPERTY_MULTIPLE_BASE = [];
    protected const PROPERTY_MULTIPLE = [];
    protected const PROPERTY_SINGLE_BASE = [];
    protected const PROPERTY_SINGLE = [];
    protected const FIELDS_NOT_FORMATTED_BASE = [
        'ID',
        'NAME',
    ];
    protected const FIELDS_NOT_FORMATTED = [];
    protected const PROPERTY_ENUM_SINGLE_BASE = [];
    protected const PROPERTY_ENUM_SINGLE = [];
    protected const CONSTRUCT_SELECT_BASE = ['*'];
    protected const CONSTRUCT_SELECT = [];
    protected const ALIAS_KEYS = [];
    /**
     * @var DataManager
     */
    protected static $table = null;
    /**
     * @var DataManager
     */
    protected static $singlePropsTable = null;
    /**
     * @var DataManager
     */
    protected static $multiplePropsTable = null;
    /** @var EO_ElementCustom */
    protected $element;
    /** @var array */
    protected $currentMultiples;
    /**
     * @var int
     */
    protected $iblockId;
    /**
     * @var string|EntityObject
     */
    protected $entity;
    /**
     * @var string|EntityObject
     */
    protected $entitySingleProps;
    /**
     * @var string|EntityObject
     */
    protected $entityMultipleProps;

    /**
     * AbstractIblock constructor.
     *
     * @param int $id
     *
     * @throws ArgumentException
     * @throws ElementNotFoundException
     * @throws IblockException
     * @throws IblockNotFoundException
     * @throws IblockPropertyNotFoundException
     * @throws NotImplementedException
     */
    public function __construct($id = 0)
    {
        if (static::$table === null || static::$singlePropsTable === null || static::$multiplePropsTable === null) {
            throw new NotImplementedException();
        }
        try {
            $this->iblockId = static::$table::getIblockId();
        } catch (ArgumentException|SystemException|IblockNotFoundException $e) {
            throw new IblockNotFoundException($e->getMessage());
        }
        $this->entity = static::$table::getObjectClass();
        $this->entitySingleProps = static::$singlePropsTable::getObjectClass();
        $this->entityMultipleProps = static::$multiplePropsTable::getObjectClass();
        $this->constructElement($id);
        $this->constructSingleProps();
        $this->constructMultipleProps($this->getFieldsMultiple());
    }

    /**
     * News constructor.
     *
     * @param int|static $entity
     *
     * @return static
     *
     * @return AbstractIblock
     * @throws ArgumentException
     * @throws ElementNotFoundException
     * @throws IblockException
     * @throws IblockNotFoundException
     * @throws IblockPropertyNotFoundException
     * @throws SystemException
     */
    public static function getObject($entity = 0)
    {
        if (is_numeric($entity) && (int)$entity > 0) {
            $entity = static::$table::getByPrimary((int)$entity, ['select' => static::getConstructSelect()])->fetchObject();
            if ($entity === null) {
                throw new ElementNotFoundException('элемент не найден');
            }
        }
        $entityClass = static::$table::getObjectClass();
        if (!($entity instanceof $entityClass)) {
            throw new ArgumentException('неверный параметр');
        }
        return new static($entity);
    }

    /**
     * @return array
     */
    public static function getConstructSelect(): array
    {
        return array_merge(static::CONSTRUCT_SELECT_BASE, static::CONSTRUCT_SELECT);
    }

    /**
     * @return array
     */
    public function getSingleProps(): array
    {
        return array_merge(static::PROPERTY_SINGLE_BASE, static::PROPERTY_SINGLE);
    }

    /**
     * @return array
     */
    public function getMultipleProps(): array
    {
        return array_merge(static::PROPERTY_MULTIPLE_BASE, static::PROPERTY_MULTIPLE);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getElement()->getId() ?? 0;
    }

    /**
     * @return UserCustom|EO_UserCustomBase|null
     */
    public function getCreatedBy(): ?UserCustom
    {
        $element = $this->getElement();
        if ($element->getCreatedByUser() === null && $element->getCreatedBy() > 0) {
            $element->fillCreatedByUser();
        }
        return $element->getCreatedByUser();
    }

    /**
     * @return int|null
     */
    public function getCreatedByReal(): ?int
    {
        $user = $this->getCreatedBy();
        return $user === null ? null : $user->getId();
    }

    /**
     * @return DateTime
     */
    public function getDateCreate(): DateTime
    {
        return $this->getElement()->getDateCreate();
    }

    /**
     * @return int
     */
    public function getDateCreateReal(): int
    {
        return $this->getDateCreate()->getTimestamp();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getElement()->getName() ?: '';
    }

    /**
     * @param string $name
     *
     * @return static|AbstractIblockInterface
     */
    public function setName(string $name): AbstractIblockInterface
    {
        $this->getElement()->setName($name);
        return $this;
    }

    /**
     * @param Result|null $result
     * @param array       $params
     *
     * @return Result
     * @throws ArgumentException
     * @throws IblockPropertyNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws SqlQueryException
     */
    public function save(Result $result = null, array $params = []): Result
    {
        if ($result === null) {
            $result = new Result();
        }
        $this->onBeforeSave($result);
        if (!$result->isSuccess()) {
            return $result;
        }
        $connection = Application::getConnection();
        $connection->startTransaction();
        $currentUserId = (int)CurrentUser::get()->getId();
        if ($currentUserId === 0) {
            $currentUserId = 1;
        }
        $changedList = [];
        $element = $this->getElement();
        if ($this->getId() === 0) {
            $new = true;
            $element->setActive(true);
            $element->setCreatedBy($currentUserId);
            $element->setModifiedBy($currentUserId);
            $element->setIblockId($this->iblockId);
        } else {
            $new = false;
            $tableFields = array_keys(static::$table::getMap());
            foreach ($this->getFieldsWrite() as $code) {
                $fieldCode = $this->getFieldCodeByCodeInElement($code);
                if (in_array($fieldCode, $tableFields, true) && $element->isChanged($fieldCode)) {
                    $changedList = $this->prepareChangeList($changedList,
                        ['CODE' => $code, 'FIELD_CODE' => $fieldCode, 'TYPE' => 'element', 'ITEM' => $element]);
                }
            }
            $element->setModifiedBy($currentUserId);
        }
        $this->onBeforeSaveElement($result);
        $singleProps = $element->getSingleProps();
        $relationCodes = array_keys($element->collectValues(Values::ALL, FieldTypeMask::RELATION));
        foreach ($relationCodes as $relationCode) {
            $element->unset($relationCode);
        }
        $res = $element->save();
        $this->onAfterSaveElement($result);
        if ($res->isSuccess()) {
            if ($this->getId() === 0) {
                $element = $this->element = static::$table::getById($res->getId())->fetchObject();
                $singleProps->setElement($element);
            }
            $this->onBeforeSaveMultipleProps($result);
            $res = $this->saveMultipleProps($changedList);
            $this->onAfterSaveMultipleProps($result);
            if (!$res->isSuccess()) {
                $result->addErrors($res->getErrors());
            } else {
                if ((int)$singleProps->getIblockElementId() === 0) {
                    $singleProps->setElement($element);
                }
                if (!$new) {
                    $tableFields = array_keys(static::$singlePropsTable::getMap());
                    foreach ($this->getFieldsWrite() as $code) {
                        if (in_array($code, $this->getSingleProps(), true)) {
                            $propId = PropertyHelper::getPropIdByCode(static::$table::getIblockId(), $code);
                            $propFieldCode = 'PROPERTY_' . $propId;
                            if ($propId > 0 && in_array($propFieldCode, $tableFields, true) && $singleProps->isChanged($propFieldCode)) {
                                $changedList = $this->prepareChangeList($changedList,
                                    ['CODE' => $code, 'FIELD_CODE' => $propFieldCode, 'TYPE' => 'single_property', 'ITEM' => $singleProps]);
                            }
                        }
                    }
                }
                $this->onBeforeSaveSingleProps($result, $singleProps);
                // фикс из-за сохранения по зависимостям - ра то рекурсия случается
                $relationCodes = array_keys($singleProps->collectValues(Values::ALL, FieldTypeMask::RELATION));
                foreach ($relationCodes as $relationCode) {
                    $singleProps->unset($relationCode);
                }
                $res = $singleProps->save();
                $this->onAfterSaveSingleProps($result);
                if (!$res->isSuccess()) {
                    /** @noinspection MissingOrEmptyGroupStatementInspection */
                    if ($res->getErrorCollection()->current()->getCode() === 'BX_ERROR' && $res->getErrorCollection()
                            ->current()
                            ->getMessage() === 'There is no data to update.') {
                        //нет измененных данных для обновления
                    } else {
                        $result->addErrors($res->getErrors());
                    }
                }
                if ($result->isSuccess()) {
                    if ($singleProps !== null) {
                        if ($singleProps->getElement() === null) {
                            $singleProps->fill();
                        }
                        $element->setSingleProps($singleProps);
                    }
                    if ($element->getSingleProps() === null) {
                        $element->fillSingleProps();
                        $element->getSingleProps()->fill();
                    }
                    $result->setData(['ID' => $this->getId(), 'NEW' => $new]);
                    $params = array_merge([
                        'CURRENT_USER_ID' => $currentUserId,
                        'CHANGE_LIST'     => $changedList,
                    ], $params);
                    $this->onAfterSave($result, $params);
                    $connection->commitTransaction();
                }
            }
        } else {
            $result->addErrors($res->getErrors());
        }
        return $result;
    }

    /**
     * @return EO_ElementCustom|mixed
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param array $data
     *
     * @return static|AbstractIblockInterface
     */
    public function setData(array $data): AbstractIblockInterface
    {
        foreach ($data as $code => $val) {
            if ($this->hasFieldWrite($code)) {
                $this->set($code, $val);
            }
        }
        return $this;
    }

    /**
     * @param $code
     *
     * @return bool
     */
    public function hasFieldRead($code): bool
    {
        return in_array($code, $this->getFieldsRead(true), true);
    }

    /**
     * @param bool $withMultipleCurrents
     *
     * @return array
     */
    public function getFieldsWrite(bool $withMultipleCurrents = false): array
    {
        if ($withMultipleCurrents) {
            return array_merge(static::FIELDS_WRITE_BASE, $this->getFieldsMultipleWithCurrent(), static::FIELDS_WRITE);
        }
        return array_merge(static::FIELDS_WRITE_BASE, $this->getFieldsMultiple(), static::FIELDS_WRITE);
    }

    /**
     * @param bool $withMultipleCurrents
     *
     * @return array
     */
    public function getFieldsRead(bool $withMultipleCurrents = false): array
    {
        if ($withMultipleCurrents) {
            return array_merge(static::FIELDS_READ_BASE, $this->getFieldsMultipleWithCurrent(), static::FIELDS_READ);
        }
        return array_merge(static::FIELDS_READ_BASE, $this->getFieldsMultiple(), static::FIELDS_READ);
    }

    /**
     * @return array
     */
    public function getFieldsMultiple(): array
    {
        return array_merge(static::PROPERTY_MULTIPLE_BASE, static::PROPERTY_MULTIPLE);
    }

    /**
     * @return array
     */
    public function getFieldsMultipleWithCurrent(): array
    {
        $fields = $this->getFieldsMultiple();
        foreach ($fields as $field) {
            $fields[] = 'CURRENT_' . $field;
        }
        return $fields;
    }

    /**
     * @param $code
     *
     * @return bool
     */
    public function hasFieldWrite($code): bool
    {
        return in_array($code, $this->getFieldsWrite(), true);
    }

    /**
     * @param      $code
     * @param bool $withCurrent
     *
     * @return bool
     */
    public function hasFieldMultiple($code, $withCurrent = false): bool
    {
        if ($withCurrent) {
            return in_array($code, $this->getFieldsMultipleWithCurrent(), true);
        }
        return in_array($code, $this->getFieldsMultiple(), true);
    }

    /**
     * @param int $formatted
     *
     * @return array
     */
    public function toArray(
        int $formatted = self::FORMATTED_TYPE['ACTUAL']
    ): array {
        $data = $this->getNotFormattedArray($formatted);
        $data = $this->formatArray($formatted, $data);
        return $data;
    }

    /**
     * @param int $formatted
     *
     * @return array
     */
    public function getNotFormattedArray(int $formatted = self::FORMATTED_TYPE['ACTUAL']): array
    {
        $data = [];
        foreach ($this->getFieldsRead() as $field) {
            $code = $field;
            try {
                $code = $this->changeKeyToAliasKey($code);
            } catch (ArgumentException $e) {
                //если не найдено просто скипаем
            }
            try {
                $field = $this->changeAliasKeyToKey($field);
            } catch (ArgumentException $e) {
                //если не найдено просто скипаем
            }
            $data[$code] = $this->get($field, $formatted);
        }
        return $data;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, ['get', 'set', 'add'], true)) {
            $codeOriginal = $arguments[0];
            $code = ToLower($codeOriginal);
            $explodeList = explode('_', $code);
            $methodNameList = [];
            foreach ($explodeList as $item) {
                $methodNameList[] = ucfirst($item);
            }
            $baseMethodName = implode('', $methodNameList);
            $methodName = $name . $baseMethodName;
            switch ($name) {
                case 'get':
                    if ($this->hasFieldRead($codeOriginal)) {
                        if (!isset($arguments[1]) || !is_int($arguments[1]) || $arguments[1] < 0) {
                            $arguments[1] = self::FORMATTED_TYPE['ACTUAL'];
                        }
                        switch ($arguments[1]) {
                            case self::FORMATTED_TYPE['FORMATTED']:
                                $tmpMethodName = $methodName . 'Formatted';
                                if (method_exists($this, $tmpMethodName) && !in_array($codeOriginal, $this->getFieldsNotFormatted(),
                                        true)) {
                                    $methodName = $tmpMethodName;
                                }
                                break;
                            case self::FORMATTED_TYPE['REAL']:
                                $tmpMethodName = $methodName . 'Real';
                                if (method_exists($this, $tmpMethodName)) {
                                    $methodName = $tmpMethodName;
                                }
                                break;
                            case self::FORMATTED_TYPE['FORMATTED_EDIT']:
                                $tmpMethodName = $methodName . 'FormattedEdit';
                                if (method_exists($this, $tmpMethodName)) {
                                    $methodName = $tmpMethodName;
                                } elseif (method_exists($this, $methodName . 'Real')) {
                                    $methodName .= 'Real';
                                }
                                break;
                            case self::FORMATTED_TYPE['FORMATTED_LIST']:
                                $tmpMethodName = $methodName . 'FormattedList';
                                if (method_exists($this, $tmpMethodName)) {
                                    $methodName = $tmpMethodName;
                                } elseif (method_exists($this, $methodName . 'Formatted')) {
                                    $methodName .= 'Formatted';
                                }
                                break;
                        }
                        $res = $this->$methodName();
                        return $res;
                    }
                    break;
                case 'set':
                    if (!$this->hasFieldWrite($codeOriginal)) {
                        try {
                            $code = ToLower($this->changeAliasKeyToKey($codeOriginal));
                            $explodeList = explode('_', $code);
                            $methodNameList = [];
                            foreach ($explodeList as $item) {
                                $methodNameList[] = ucfirst($item);
                            }
                            $baseMethodName = implode('', $methodNameList);
                            $methodName = $name . $baseMethodName;
                        } catch (ArgumentException $e) {
                        }
                    }
                    if ($this->hasFieldWrite($codeOriginal)) {
                        return $this->$methodName($arguments[1]);
                    }
                    break;
                case 'add':
                    if ($this->hasFieldMultiple($codeOriginal, true)) {
                        return $this->$methodName($arguments[1]);
                    }
                    break;
            }
        }
        return null;
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws IblockException
     * @throws IblockNotFoundException
     * @throws IblockPropertyNotFoundException
     * @throws SystemException
     */
    public function isChanged(): bool
    {
        return $this->isChangedElement() || $this->isChangedSingleProps() || $this->isChangedMultipleProps();
    }

    /**
     * @param int   $formatted
     * @param array $data
     *
     * @return array
     */
    public function formatArray(int $formatted, array $data): array
    {
        if ($formatted === self::FORMATTED_TYPE['FORMATTED_LIST']) {
            $this->toArrayFormattedList($data);
        } elseif ($formatted === self::FORMATTED_TYPE['FORMATTED_EDIT']) {
            $this->toArrayFormattedEdit($data);
        } elseif ($formatted === self::FORMATTED_TYPE['FORMATTED']) {
            $this->toArrayFormatted($data);
        } elseif ($formatted === self::FORMATTED_TYPE['ACTUAL']) {
            $this->toArrayFormattedActual($data);
        } elseif ($formatted === self::FORMATTED_TYPE['REAL']) {
            $this->toArrayFormattedReal($data);
        }
        return $data;
    }

    /**
     * @param $code
     *
     * @return string
     */
    protected function getFieldCodeByCodeInElement($code): string
    {
        return $code;
    }

    /**
     * @return array
     */
    protected function getFieldsNotFormatted(): array
    {
        return array_merge(static::FIELDS_NOT_FORMATTED_BASE, static::FIELDS_NOT_FORMATTED);
    }

    /**
     * @param $id
     *
     * @throws ElementNotFoundException
     */
    protected function constructElement($id): void
    {
        if ($id > 0 || $id instanceof $this->entity) {
            if (is_numeric($id)) {
                try {
                    $this->element = static::$table::getByPrimary($id, [
                        'select' => static::getConstructSelect(),
                        'filter' => ['ACTIVE' => BitrixUtils::BX_BOOL_TRUE],
                    ])->fetchObject();
                    if ($this->element === null) {
                        throw new ElementNotFoundException('не удалось установить элемент');
                    }
                } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
                    throw new ElementNotFoundException($e->getMessage());
                }
            } else {
                $this->element = $id;
            }

        } else {
            $this->element = new $this->entity();
        }
    }

    /**
     * @throws ArgumentException
     * @throws IblockException
     * @throws IblockNotFoundException
     * @throws IblockPropertyNotFoundException
     */
    protected function constructSingleProps(): void
    {
        $element = $this->getElement();
        if ($this->getId() > 0) {
            $singleProps = $element->getSingleProps();
            if ($singleProps === null) {
                $singleProps = $element->fillSingleProps();
            }
            if ($singleProps === null) {
                $singleProps = new $this->entitySingleProps();
            }
            $singleProps->setElement($element);
            $element->setSingleProps($singleProps);
        } else {
            $singleProps = new $this->entitySingleProps();
            $singleProps->setElement($element);
            $element->setSingleProps($singleProps);
        }
    }

    /**
     * @param array $changedList
     *
     * @return Result
     */
    protected function saveMultipleProps(array &$changedList): Result
    {
        $result = new Result();
        foreach ($this->getFieldsMultiple() as $code) {
            $res = $this->saveMultipleProp($code, 'CURRENT_' . $code, $changedList);
            if (!$res->isSuccess()) {
                $result->addErrors($res->getErrors());
            }
        }
        return $result;
    }

    /** @todo переделать */
    /**
     * @param       $code
     * @param       $currentCode
     *
     * @param array $changedList
     *
     * @return Result
     */
    protected function saveMultipleProp($code, $currentCode, array &$changedList): Result
    {
        $result = new Result();
        /** @var Image[]|UserCustom[] $items */
        $items = $this->get($code);
        $delVals = $currentVals = $this->get($currentCode);
        $addList = $newList = [];
        $params = [
            'CODE'         => $code,
            'ITEMS'        => $items,
            'DEL_VALS'     => $delVals,
            'CURRENT_VALS' => $currentVals,
            'ADD_LIST'     => $addList,
            'NEW_LIST'     => $newList,
            'RESULT'       => $result,
        ];
        if (!empty($items)) {
            [$newList, $addList, $delVals, $currentVals] = $this->prepareSaveMultiplePropData($params);
        }
        if (!empty($addList) || !empty($delVals)) {
            $changedList[$code] = [
                'DEL' => array_values($delVals),
                'ADD' => $addList,
                'OLD' => array_values($currentVals),
                'NEW' => $newList,
            ];
        }
        if (!empty($delVals)) {
            foreach ($delVals as $propItemId => $value) {
                $this->deleteMultiplePropItem($params, $propItemId, $value);
            }
        }
        return $result;
    }

    /**
     * @param int $id
     * @param     $result
     */
    protected function deleteMultipleProp(int $id, Result $result): void
    {
        try {
            $res = static::$multiplePropsTable::delete($id);
            if (!$res->isSuccess()) {
                $result->addErrors($res->getErrors());
            }
        } catch (Exception $e) {
            $result->addError(new Error($e->getMessage(), $e->getCode(), ['trace' => $e->getTrace()]));
        }
    }

    /**
     * @param Image|UserCustom $item
     * @param Result           $result
     * @param string           $code
     */
    protected function addMultipleProp($item, Result $result, string $code): void
    {
        try {
            $res = static::$multiplePropsTable::add([
                'IBLOCK_PROPERTY_ID' => PropertyHelper::getPropIdByCode(static::$table::getIblockId(), $code),
                'IBLOCK_ELEMENT_ID'  => $this->getId(),
                'VALUE'              => $item->getId(),
            ]);
            if (!$res->isSuccess()) {
                $result->addErrors($res->getErrors());
            }
        } catch (Exception $e) {
            $result->addError(new Error($e->getMessage(), $e->getCode(), ['trace' => $e->getTrace()]));
        }
    }

    /**
     * @param $propCode
     * @param $vals
     *
     */
    protected function setMultiplePropValues($propCode, $vals = null): void
    {
        $currentPropCode = 'CURRENT_' . $propCode;
        if ($vals !== null && is_array($vals) && !empty($vals)) {
            $ids = [];
            foreach ($vals as $item) {
                $this->add($currentPropCode, $item);
                $ids[(int)$item['ID']] = (int)$item['VALUE'];
            }
            if (empty($ids)) {
                $ids = [];
            }
            $this->set($propCode, $ids);
        } else {
            $this->set($currentPropCode, []);
            $this->set($propCode, []);
        }
    }

    /**
     * @param array $list
     *
     * @throws IblockPropertyNotFoundException
     */
    protected function constructMultipleProps(array $list): void
    {
        $element = $this->getElement();
        if ($this->getId() > 0 && $element->getMultipleProps() === null) {
            $element->fillMultipleProps();
        }
        $multipleProps = $element->getMultipleProps();
        $vals = [];
        $setValues = [];
        if ($multipleProps !== null) {
            /** @var static::$entityMultipleProps $multipleProp */
            foreach ($multipleProps as $multipleProp) {
                $propCode = PropertyHelper::getPropCodeById(static::$table::getIblockId(), $multipleProp->getIblockPropertyId());
                if (!isset($setValues[$propCode])) {
                    $setValues[$propCode] = [];
                }
                if (!isset($vals[$propCode])) {
                    $vals[$propCode] = [];
                }
                if (!in_array($multipleProp->getValue(), $setValues[$propCode], true)) {
                    $vals[$propCode][] = [
                        'ID'    => $multipleProp->getId(),
                        'VALUE' => $multipleProp->getValue(),
                    ];
                }
            }
            unset($setValues);
        }
        foreach ($list as $code) {
            $this->setMultiplePropValues($code, $vals[$code]);
        }
    }

    /**
     * @return array
     */
    protected function getEnumSinglePropCodes(): array
    {
        return array_merge(static::PROPERTY_ENUM_SINGLE_BASE, static::PROPERTY_ENUM_SINGLE);
    }

    /**
     * @param string                    $code
     * @param                           $val
     *
     * @param EO_ElementCustom|null     $singleProps
     *
     * @return $this
     * @throws IblockPropertyNotFoundException
     */
    protected function setSingleProperty(string $code, $val, $singleProps = null): self
    {
        if ($singleProps === null) {
            $singleProps = $this->getElement()->getSingleProps();
        }
        if ($singleProps !== null) {
            $iblockId = static::$table::getIblockId();
            $propId = PropertyHelper::getPropIdByCode($iblockId, $code);
            $singleProps->set('PROPERTY_' . $propId, $val);
        }
        return $this;
    }

    /**
     * @param string $code
     *
     * @return mixed
     * @throws IblockPropertyNotFoundException
     */
    protected function getSingleProperty(string $code)
    {
        return $this->getElement()->getSingleProps()->get('PROPERTY_' . PropertyHelper::getPropIdByCode(static::$table::getIblockId(),
                $code));
    }

    /**
     * @param array $changedList
     * @param array $params
     *
     * @return array
     */
    protected function prepareChangeList(array $changedList, array $params): array
    {
        $code = $params['CODE'];
        $fieldCode = $params['FIELD_CODE'];
        /** @var static::$entity|static::$entitySingleProps $item */
        $item = $params['ITEM'];
        $oldVal = $item->remindActual($fieldCode);
        $newVal = $item->get($fieldCode);
        $this->prepareChangeListData($code, $oldVal, $newVal);
        if (is_array($oldVal) || is_array($newVal)) {
            /** @noinspection NotOptimalIfConditionsInspection */
            if (!is_array($oldVal)) {
                $oldVal = [];
            }
            /** @noinspection NotOptimalIfConditionsInspection */
            if (!is_array($newVal)) {
                $newVal = [];
            }
            $biggerArray = count($newVal) > count($oldVal) ? $newVal : $oldVal;
            foreach ($biggerArray as $key => $value) {
                if ($oldVal[$key] === $newVal[$key]) {
                    unset($oldVal[$key], $newVal[$key]);
                }
            }
        }
        if ($oldVal !== $newVal) {
            $changedList[$code] = [
                'OLD' => $oldVal,
                'NEW' => $newVal,
            ];
        }
        return $changedList;
    }

    /**
     * @return bool
     */
    protected function isChangedElement(): bool
    {
        $element = $this->getElement();
        $tableFields = array_keys(static::$table::getMap());
        foreach ($this->getFieldsWrite() as $code) {
            $fieldCode = $this->getFieldCodeByCodeInElement($code);
            if (in_array($fieldCode, $tableFields, true) && $element->isChanged($fieldCode)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     * @throws IblockPropertyNotFoundException
     */
    protected function isChangedSingleProps(): bool
    {
        $singleProps = $this->getElement()->getSingleProps();
        $tableFields = array_keys(static::$singlePropsTable::getMap());
        foreach ($this->getFieldsWrite() as $code) {
            if (in_array($code, $this->getSingleProps(), true)) {
                $propId = PropertyHelper::getPropIdByCode(static::$table::getIblockId(), $code);
                $propFieldCode = 'PROPERTY_' . $propId;
                if ($propId > 0 && in_array($propFieldCode, $tableFields, true) && $singleProps->isChanged($propFieldCode)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function isChangedMultipleProps(): bool
    {
        $isChanged = false;
        foreach ($this->getFieldsMultiple() as $code) {
            /** @var Image[]|UserCustom[] $items */
            $currentCode = 'CURRENT_' . $code;
            $items = $this->get($code);
            $delVals = $currentVals = $this->get($currentCode);
            $addList = [];
            if (!empty($items)) {
                foreach ($items as $item) {
                    if (!in_array($item->getId(), $currentVals, true)) {
                        $addList[] = $item->getId();
                    } else {
                        unset($delVals[array_search($item->getId(), $delVals, true)]);
                    }
                }
            }
            if (!empty($addList) || !empty($delVals)) {
                $isChanged = true;
            }
        }
        return $isChanged;
    }

    /**
     * @param Result $result
     */
    protected function onBeforeSave(Result $result): void
    {
        $event = new Event(static::EVENT_MODULE_ID, 'onBeforeIblockSave');
        $event->setParameter('ENTITY', $this);
        $event->send();
        // Обработка результатов вызова
        if ($event->getResults()) {
            /** @var EventResult $eventResult */
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === EventResult::ERROR) {
                    $params = $eventResult->getParameters();
                    if ($params instanceof ErrorCollection) {
                        $result->addErrors($params->toArray());
                    } elseif ($params instanceof Error) {
                        $result->addError($params);
                    } else {
                        if (is_array($params) && current($params) instanceof Error) {
                            $result->addErrors($params);
                        } else {
                            $errorMessage = 'Ошибка обработки в событии onBeforeIblockSave';
                            if (is_string($params)) {
                                $errorMessage .= ' ' . $params;
                            }
                            $result->addError(new Error($errorMessage));
                        }
                    }
                    return;
                }
            }
        }
    }

    /**
     * @param Result $result
     * @param array  $params
     */
    protected function onAfterSave(Result $result, array $params = []): void
    {
        $event = new Event(static::EVENT_MODULE_ID, 'onAfterIblockSave');
        $event->setParameter('ENTITY', $this);
        $event->setParameter('EVENT_PARAMS', $params);
        $event->send();
    }

    /**
     * @param Result $result
     */
    protected function onBeforeSaveElement(Result $result): void
    {
        $event = new Event(static::EVENT_MODULE_ID, 'onBeforeIblockElementSave');
        $event->setParameter('ENTITY', $this);
        $event->send();
        // Обработка результатов вызова
        if ($event->getResults()) {
            /** @var EventResult $eventResult */
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === EventResult::ERROR) {
                    $params = $eventResult->getParameters();
                    if ($params instanceof ErrorCollection) {
                        $result->addErrors($params->toArray());
                    } elseif ($params instanceof Error) {
                        $result->addError($params);
                    } else {
                        if (is_array($params) && current($params) instanceof Error) {
                            $result->addErrors($params);
                        } else {
                            $errorMessage = 'Ошибка обработки в событии onBeforeIblockSave';
                            if (is_string($params)) {
                                $errorMessage .= ' ' . $params;
                            }
                            $result->addError(new Error($errorMessage));
                        }
                    }
                    return;
                }
            }
        }
    }

    /**
     * @param Result $result
     * @param array  $params
     */
    protected function onAfterSaveElement(Result $result, array $params = []): void
    {
        $event = new Event(static::EVENT_MODULE_ID, 'onAfterIblockElementSave');
        $event->setParameter('ENTITY', $this);
        $event->setParameter('EVENT_PARAMS', $params);
        $event->send();
        // Обработка результатов вызова
        if ($event->getResults()) {
            /** @var EventResult $eventResult */
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === EventResult::ERROR) {
                    $params = $eventResult->getParameters();
                    if ($params instanceof ErrorCollection) {
                        $result->addErrors($params->toArray());
                    } elseif ($params instanceof Error) {
                        $result->addError($params);
                    } else {
                        if (is_array($params) && current($params) instanceof Error) {
                            $result->addErrors($params);
                        } else {
                            $errorMessage = 'Ошибка обработки в событии onBeforeIblockSave';
                            if (is_string($params)) {
                                $errorMessage .= ' ' . $params;
                            }
                            $result->addError(new Error($errorMessage));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Result $result
     */
    protected function onBeforeSaveMultipleProps(Result $result): void
    {
        $event = new Event(static::EVENT_MODULE_ID, 'onBeforeIblockMultiplePropertySave');
        $event->setParameter('ENTITY', $this);
        $event->send();
        // Обработка результатов вызова
        if ($event->getResults()) {
            /** @var EventResult $eventResult */
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === EventResult::ERROR) {
                    $params = $eventResult->getParameters();
                    if ($params instanceof ErrorCollection) {
                        $result->addErrors($params->toArray());
                    } elseif ($params instanceof Error) {
                        $result->addError($params);
                    } else {
                        if (is_array($params) && current($params) instanceof Error) {
                            $result->addErrors($params);
                        } else {
                            $errorMessage = 'Ошибка обработки в событии onBeforeIblockSave';
                            if (is_string($params)) {
                                $errorMessage .= ' ' . $params;
                            }
                            $result->addError(new Error($errorMessage));
                        }
                    }
                    return;
                }
            }
        }
    }

    /**
     * @param Result $result
     * @param array  $params
     */
    protected function onAfterSaveMultipleProps(Result $result, array $params = []): void
    {
        $event = new Event(static::EVENT_MODULE_ID, 'onAfterIblockMultiplePropertySave');
        $event->setParameter('ENTITY', $this);
        $event->setParameter('EVENT_PARAMS', $params);
        $event->send();
        // Обработка результатов вызова
        if ($event->getResults()) {
            /** @var EventResult $eventResult */
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === EventResult::ERROR) {
                    $params = $eventResult->getParameters();
                    if ($params instanceof ErrorCollection) {
                        $result->addErrors($params->toArray());
                    } elseif ($params instanceof Error) {
                        $result->addError($params);
                    } else {
                        if (is_array($params) && current($params) instanceof Error) {
                            $result->addErrors($params);
                        } else {
                            $errorMessage = 'Ошибка обработки в событии onBeforeIblockSave';
                            if (is_string($params)) {
                                $errorMessage .= ' ' . $params;
                            }
                            $result->addError(new Error($errorMessage));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Result $result
     */
    protected function onBeforeSaveSingleProps(Result $result, $singleProps = null): void
    {
        $event = new Event(static::EVENT_MODULE_ID, 'onBeforeIblockSinglePropertySave');
        $event->setParameter('ENTITY', $this);
        $event->send();
        // Обработка результатов вызова
        if ($event->getResults()) {
            /** @var EventResult $eventResult */
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === EventResult::ERROR) {
                    $params = $eventResult->getParameters();
                    if ($params instanceof ErrorCollection) {
                        $result->addErrors($params->toArray());
                    } elseif ($params instanceof Error) {
                        $result->addError($params);
                    } else {
                        if (is_array($params) && current($params) instanceof Error) {
                            $result->addErrors($params);
                        } else {
                            $errorMessage = 'Ошибка обработки в событии onBeforeIblockSave';
                            if (is_string($params)) {
                                $errorMessage .= ' ' . $params;
                            }
                            $result->addError(new Error($errorMessage));
                        }
                    }
                    return;
                }
            }
        }
    }

    /**
     * @param Result $result
     * @param array  $params
     */
    protected function onAfterSaveSingleProps(Result $result, array $params = []): void
    {
        $event = new Event(static::EVENT_MODULE_ID, 'onAfterIblockSinglePropertySave');
        $event->setParameter('ENTITY', $this);
        $event->setParameter('EVENT_PARAMS', $params);
        $event->send();
        // Обработка результатов вызова
        if ($event->getResults()) {
            /** @var EventResult $eventResult */
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === EventResult::ERROR) {
                    $params = $eventResult->getParameters();
                    if ($params instanceof ErrorCollection) {
                        $result->addErrors($params->toArray());
                    } elseif ($params instanceof Error) {
                        $result->addError($params);
                    } else {
                        if (is_array($params) && current($params) instanceof Error) {
                            $result->addErrors($params);
                        } else {
                            $errorMessage = 'Ошибка обработки в событии onBeforeIblockSave';
                            if (is_string($params)) {
                                $errorMessage .= ' ' . $params;
                            }
                            $result->addError(new Error($errorMessage));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $data
     */
    protected function toArrayFormattedActual(array $data): void
    {
    }

    /**
     * @param array $data
     */
    protected function toArrayFormattedReal(array $data): void
    {
    }

    /**
     * @param array $data
     */
    protected function toArrayFormatted(array &$data): void
    {

    }

    /**
     * @param array $data
     */
    protected function toArrayFormattedList(array &$data): void
    {
        $data['DESCRIPTION'] = TruncateText($data['DESCRIPTION'], 90);
        $data['NAME'] = TruncateText($data['NAME'], 90);
    }

    /**
     * @param array $data
     */
    protected function toArrayFormattedEdit(array &$data): void
    {
        $data = [
            'FIELDS' => $data,
        ];
    }

    /**
     * @param string $code
     * @param        $oldVal
     * @param        $newVal
     */
    protected function prepareChangeListData(string $code, &$oldVal, &$newVal): void
    {
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function prepareSaveMultiplePropData(array $params): array
    {
        $code = $params['CODE'];
        $items = $params['ITEMS'];
        $delVals = $params['DEL_VALS'];
        $currentVals = $params['CURRENT_VALS'];
        $addList = $params['ADD_LIST'];
        $newList = $params['NEW_LIST'];
        $result = $params['RESULT'];
        foreach ($items as $item) {
            $newList[] = $item->getId();
            if (!in_array($item->getId(), $currentVals, true)) {
                $this->addMultipleProp($item, $result, $code);
                $addList[] = $item->getId();
            } else {
                unset($delVals[array_search($item->getId(), $delVals, true)]);
            }
        }
        return [$newList, $addList, $delVals, $currentVals];
    }

    /**
     * @param array $params
     * @param       $propItemId
     * @param       $value
     */
    protected function deleteMultiplePropItem(array $params, $propItemId, $value): void
    {
        $result = $params['RESULT'];
        $this->deleteMultipleProp($propItemId, $result);
    }


    /**
     * @param EO_PropertyEnumeration $enum
     *
     * @return string
     */
    protected function getEnumFormattedValue(EO_PropertyEnumeration $enum): string
    {
        if ($enum === null) {
            return '';
        }
        return ucfirst($enum->getValue());
    }

    /**
     * @param string $code
     *
     * @return string
     * @throws ArgumentException
     */
    protected function changeKeyToAliasKey(string $code): string
    {
        if (array_key_exists($code, static::ALIAS_KEYS)) {
            return static::ALIAS_KEYS[$code];
        }
        throw new ArgumentException('алиас не найден');
    }

    /**
     * @param string $code
     *
     * @return string
     * @throws ArgumentException
     */
    protected function changeAliasKeyToKey(string $code): string
    {
        if (in_array($code, static::ALIAS_KEYS, true)) {
            return array_search($code, static::ALIAS_KEYS, true);
        }
        throw new ArgumentException('алиас не найден');
    }
}
