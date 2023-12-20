<?php

namespace NotaTools\Interfaces\Orm\Collection;

use InvalidArgumentException;
use NotaTools\Exception\Orm\Model\ModelException;
use NotaTools\Orm\Collection\ImageCollection;

/**
 * Class ImageCollectionInterface
 * @package NotaTools\Interfaces\Orm\Collection
 */
interface ImageCollectionInterface
{
    /**
     * @param array $ids
     *
     * @return ImageCollection
     * @throws ModelException
     */
    public static function createFromIds(array $ids = []): ImageCollection;

    /**
     * Dirty hack
     *
     * @throws InvalidArgumentException
     */
    public static function createNoImageCollection(): ImageCollection;

    /**
     * @param     $size
     * @param int $resizeType
     *
     * @return ImageCollection
     */
    public function getResizeCollection($size, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL): ImageCollection;
}
