<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Collection;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\FileTable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Exception;
use InvalidArgumentException;
use NotaTools\Exception\Orm\Model\ModelException;
use NotaTools\Interfaces\Orm\Collection\ImageCollectionInterface;
use NotaTools\Interfaces\Orm\Model\FileInterface;
use NotaTools\Interfaces\Orm\Model\ImageInterface;
use NotaTools\Orm\Model\File;
use NotaTools\Orm\Model\Image;

/**
 * Class FileCollection
 * @package NotaTools\Orm\Collection
 */
class FileCollection extends ObjectArrayCollection
{
    /**
     * @param array $ids
     *
     * @return FileCollection
     * @throws ModelException
     */
    public static function createFromIds(array $ids = []): FileCollection
    {
        $collection = new static();
        if (!empty($ids)) {
            try {
                $result = FileTable::query()->addFilter('ID', $ids)->addSelect('*')->exec();
                $items = [];
                while ($item = $result->fetch()) {
                    $items[(int)$item['ID']] = $item;
                }
            } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
                throw new ModelException($e->getMessage(), $e->getCode(), $e);
            }
            foreach ($ids as $id) {
                if (array_key_exists((int)$id, $items)) {
                    $collection->add(new File($items[(int)$id]));
                }
            }
        }
        return $collection;
    }

    /**
     * @param mixed $object
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkType($object): void
    {
        if (!($object instanceof FileInterface)) {
            throw new InvalidArgumentException('Переданный объект не является файлом');
        }
    }
}
