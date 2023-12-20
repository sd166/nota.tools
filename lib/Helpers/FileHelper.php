<?php /** @noinspection PhpUnused */

namespace NotaTools\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\FileNotFoundException;
use CFile;
use NotaTools\Exception\File\FileException;
use NotaTools\Exception\File\FileExtensionException;
use NotaTools\Exception\File\FileMimeTypeException;
use NotaTools\Exception\File\FileNotFoundException as FileNotFoundExceptionNota;
use NotaTools\Exception\File\FileSaveException;

/**
 * Class FileHelper
 * @package NotaTools\Helpers
 */
class FileHelper
{
    /**
     * @param array $params
     *
     * @return int
     * @throws ArgumentException
     * @throws FileNotFoundExceptionNota
     * @throws FileExtensionException
     * @throws FileMimeTypeException
     * @throws FileSaveException
     */
    public static function saveFile(array $params): int
    {
        $imageId = 0;
        if (empty($params['filePathFull'])) {
            throw new ArgumentException('не задан путь к файлу');
        }
        if (empty($params['module'])) {
            $params['module'] = 'main';
        }
        $file = new File($params['filePathFull']);
        if (static::checkFileType(['file' => $file, 'checkParams' => $params['checkParams']])) {
            $makeFileArray = CFile::MakeFileArray($params['filePathFull']);
            $makeFileArray['MODULE_ID'] = $params['module'];
            if (!empty($params['name'])) {
                $makeFileArray['name'] = $params['name'];
            }
            $imageId = (int)CFile::SaveFile($makeFileArray, 'sevensuns');
        }
        if ($imageId === 0) {
            throw new FileSaveException();
        }
        return $imageId;
    }

    /**
     * @param string $filePathFull
     * @param string $module
     *
     * @return int
     * @throws ArgumentException
     * @throws FileNotFoundExceptionNota
     * @throws FileExtensionException
     * @throws FileMimeTypeException
     * @throws FileSaveException
     */
    public static function saveImage(string $filePathFull, string $module = 'main'): int
    {
        if (empty($filePathFull)) {
            throw new ArgumentException('не задан путь к файлу');
        }
        return static::saveFile([
            'filePathFull' => $filePathFull,
            'module'       => $module,
            'checkParams'  => [
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'mimes'      => ['image/jpeg', 'image/png', 'image/gif'],
            ],
        ]);
    }

    /**
     * @param array $params
     *
     * @return File
     * @throws ArgumentException
     * @throws FileExtensionException
     * @throws FileMimeTypeException
     * @throws FileNotFoundExceptionNota
     * @throws FileException
     */
    public static function saveTempFile(array $params): File
    {
        $originalFile = new File($params['name']);
        if (empty($params['data'])) {
            throw new ArgumentException('на указаны данные для сохранения');
        }
        if (empty($params['module'])) {
            $params['module'] = 'main';
        }
        $filePath = '/upload/tmp/' . $params['module'] . '/' . md5(mt_rand());
        $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePath;
        $file = new File($filePathFull);
        $delFile = true;
        if (!empty($params['checkParams']['extensions']) && static::checkFileType([
                'file'        => $originalFile,
                'checkParams' => ['extensions' => $params['checkParams']['extensions']],
            ])) {
            unset($params['checkParams']['extensions']);
        }
        if ($file->putContents(base64_decode($params['data'])) !== false) {
            try {
                if (static::checkFileType(['file' => $file, 'checkParams' => $params['checkParams']])) {
                    $delFile = false;
                    return $file;
                }
            } finally {
                if ($delFile) {
                    $file->delete();
                }
            }
        } else {
            throw new FileException();
        }
        throw new FileException();
    }

    /**
     * @param array $params
     *
     * @return int
     * @throws ArgumentException
     * @throws FileNotFoundExceptionNota
     * @throws FileException
     * @throws FileExtensionException
     * @throws FileMimeTypeException
     * @throws FileSaveException
     */
    public static function saveFileRest(array $params): int
    {
        if (!isset($params['data'], $params['name'])) {
            throw new ArgumentException('не заполнены обязательные параметры');
        }
        if (!isset($params['checkParams'])) {
            $params['checkParams'] = [];
        }
        if (!isset($params['module'])) {
            $params['module'] = 'main';
        }
        $file = static::saveTempFile([
            'data'        => $params['data'],
            'module'      => $params['module'],
            'name'        => $params['name'] ?: '',
            'checkParams' => $params['checkParams'],
        ]);
        return static::saveFile([
            'filePathFull' => $file->getPath(),
            'module'       => $params['module'],
            'name'         => $params['name'] ?: '',
        ]);
    }

    /**
     * @param string $fileName
     * @param string $fileData
     * @param string $module
     *
     * @return int
     * @throws ArgumentException
     * @throws FileNotFoundExceptionNota
     * @throws FileException
     * @throws FileExtensionException
     * @throws FileMimeTypeException
     * @throws FileSaveException
     */
    public static function saveImageRest(string $fileName, string $fileData, string $module = 'main'): int
    {
        if (empty($fileName) || empty($fileData)) {
            throw new ArgumentException('не заполнены обязательные параметры');
        }
        return static::saveFileRest([
            'data'        => $fileData,
            'name'        => $fileName,
            'module'      => $module,
            'checkParams' => [
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'mimes'      => ['image/jpeg', 'image/png', 'image/gif'],
            ],
        ]);
    }

    /**
     * @param array $params
     *
     * @return bool
     * @throws FileExtensionException
     * @throws FileMimeTypeException
     * @throws ArgumentException
     * @throws FileNotFoundExceptionNota
     */
    protected static function checkFileType(array $params): bool
    {
        if (!isset($params['file']) || !($params['file'] instanceof File)) {
            throw new ArgumentException('не указан файл');
        }
        /** @var File $file */
        $file = $params['file'];
        if (!isset($params['checkParams'])) {
            $params['checkParams'] = [];
        }
        $checkParams = $params['checkParams'];
        $res = true;
        if (!empty($checkParams['extensions'])) {
            $extension = $file->getExtension();
            if (empty($extension) || !in_array($extension, $checkParams['extensions'], true)) {
                throw new FileExtensionException();
            }
        }
        if (!empty($checkParams['mimes'])) {
            try {
                $contentType = (string)$file->getContentType();
            } catch (FileNotFoundException $e) {
                throw new FileNotFoundExceptionNota();
            }
            if (empty($contentType) || !in_array($contentType, $checkParams['mimes'], true)) {
                throw new FileMimeTypeException();
            }
        }
        return $res;
    }
}