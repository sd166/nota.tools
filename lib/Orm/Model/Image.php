<?php /** @noinspection PhpUnused */

namespace NotaTools\Orm\Model;

use Bitrix\Main\ArgumentException;
use CFile;
use NotaTools\Enum\MediaEnum;
use NotaTools\Interfaces\Orm\Model\ImageInterface;

/**
 * Class Image
 *
 * @package NotaTools\Orm\Model
 */
class Image extends File implements ImageInterface
{
    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;

    /** @var null|ImageInterface */
    protected $original;

    /**
     * Image constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        $fields = array_change_key_case($fields, CASE_UPPER);
        if (!empty($fields['WIDTH'])) {
            $this->setWidth($fields['WIDTH']);
        }
        if (!empty($fields['HEIGHT'])) {
            $this->setHeight($fields['HEIGHT']);
        }
        parent::__construct($fields);
    }

    /**
     * @return ImageInterface
     */
    public static function getNoImage(): ImageInterface
    {
        return new static([
            'src'    => MediaEnum::NO_IMAGE_WEB_PATH,
            'width'  => MediaEnum::NO_IMAGE_WIDTH,
            'height' => MediaEnum::NO_IMAGE_HEIGHT,
        ]);
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return (int)$this->width;
    }

    /**
     * @param int $width
     *
     * @return static
     */
    protected function setWidth($width): ImageInterface
    {
        $this->width = (int)$width;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return (int)$this->height;
    }

    /**
     * @param int $height
     *
     * @return static
     */
    protected function setHeight($height): ImageInterface
    {
        $this->height = (int)$height;
        return $this;
    }

    /**
     * @param array $size
     * @param int   $resizeType
     *
     * @return ImageInterface
     * @throws ArgumentException
     */
    public function getResizeImage(array $size, int $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL): ImageInterface
    {
        $size = array_change_key_case($size, CASE_LOWER);
        if (empty($size) || (empty($size['width']) && empty($size['height'])) || ((int)$size['width'] === 0 && (int)$size['height'] === 0)) {
            throw new ArgumentException('не заданы размеры ресайза');
        }
        //косталь - если хотим отресайзить по одной тсороне
        if ((int)$size['width'] === 0) {
            $size['width'] = $size['height'] * 100;
        } elseif ((int)$size['height'] === 0) {
            $size['height'] = $size['width'] * 100;
        }
        $resizedFile = CFile::ResizeImageGet($this->getId(), $size, $resizeType, true);
        $fields = array_change_key_case($resizedFile, CASE_UPPER);
        $resizeObj = new static($fields);
        $resizeObj->setOriginal($this);
        return $resizeObj;
    }

    /**
     * @return ImageInterface|null
     */
    public function getOriginal(): ?ImageInterface
    {
        return $this->original;
    }

    /**
     * @param ImageInterface $original
     *
     * @return ImageInterface
     */
    public function setOriginal(ImageInterface $original): ImageInterface
    {
        $this->original = $original;
        return $this;
    }
}
