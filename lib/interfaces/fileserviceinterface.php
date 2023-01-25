<?php

namespace Bx\Model\Interfaces;

use Bx\Model\Models\File;
use Bx\Model\ModelCollection;
use Psr\Http\Message\UploadedFileInterface;

interface FileServiceInterface extends ModelServiceInterface
{
    /**
     * @param string $baseDir
     * @param string ...$filePaths
     * @return File[]|ModelCollection
     */
    public function saveFiles(string $baseDir, string ...$filePaths): ModelCollection;
    /**
     * @param string $baseDir
     * @param UploadedFileInterface ...$files
     * @return File[]|ModelCollection
     */
    public function saveUploadFiles(string $baseDir, UploadedFileInterface ...$files): ModelCollection;
    /**
     * @param File[]|ModelCollection $collection
     * @param integer $width
     * @param integer $height
     * @param integer|null $mode
     * @return void
     */
    public function resizeImageCollection(ModelCollection $collection, int $width, int $height, ?int $mode = null);
    /**
     * @param File $image
     * @param integer $width
     * @param integer $height
     * @param integer|null $mode
     * @return void
     */
    public function resizeImage(File $image, int $width, int $height, ?int $mode = null);

    public function makeDataForSaveFile(UploadedFileInterface $file): array;
}
