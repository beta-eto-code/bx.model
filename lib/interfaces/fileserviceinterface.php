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
}
