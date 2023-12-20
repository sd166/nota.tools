<?php

namespace NotaTools\Interfaces\Orm\Model;

/**
 * Interface ImageInterface
 *
 * @package NotaTools\Interfaces\Orm\Model
 */
interface ImageInterface extends FileInterface
{

    /**
     * @return ImageInterface
     */
    public static function getNoImage(): ImageInterface;

    /**
     * @return int
     */
    public function getHeight(): int;

    /**
     * @return int
     */
    public function getWidth(): int;

    /**
     * @param array $size
     * @param int   $resizeType
     *
     * @return ImageInterface
     */
    public function getResizeImage(array $size, int $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL): ImageInterface;

    /**
     * @return ImageInterface|null
     */
    public function getOriginal(): ?ImageInterface;

    /**
     * @param ImageInterface $original
     *
     * @return ImageInterface
     */
    public function setOriginal(ImageInterface $original): ImageInterface;
}
