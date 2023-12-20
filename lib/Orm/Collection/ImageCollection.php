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
use NotaTools\Interfaces\Orm\Model\ImageInterface;
use NotaTools\Orm\Model\Image;

/**
 * Class ImageCollection
 * @package NotaTools\Orm\Collection
 */
class ImageCollection extends ObjectArrayCollection implements ImageCollectionInterface
{
    /**
     * @param array $ids
     *
     * @return ImageCollection
     * @throws ModelException
     */
    public static function createFromIds(array $ids = []): ImageCollection
    {
        if (!empty($ids)) {
            $collection = new static();
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
                    $collection->add(new Image($items[(int)$id]));
                }
            }
        } else {
            $collection = static::createNoImageCollection();
        }
        return $collection;
    }

    /**
     * Dirty hack
     *
     * @throws InvalidArgumentException
     */
    public static function createNoImageCollection(): ImageCollection
    {
        $collection = new static();
        $collection->add(Image::getNoImage());
        return $collection;
    }

    /**
     * @param     $size
     * @param int $resizeType
     *
     * @return ImageCollection
     * @throws Exception
     */
    public function getResizeCollection($size, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL): ImageCollection
    {
        $collection = new static();
        /** @var ImageInterface $item */
        foreach ($this->getIterator() as $item) {
            $collection->add($item->getResizeImage($size, $resizeType));
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
        if (!($object instanceof ImageInterface)) {
            throw new InvalidArgumentException('Переданный объект не является картинкой');
        }
    }
}
