<?php

namespace Bx\Model\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\ModelCollection;
use Bitrix\Main\FileTable;
use Bitrix\Main\Result;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\BaseModelService;
use Bx\Model\Models\File;
use Bx\Model\Traits\FilterableHelper;
use Bx\Model\Traits\LimiterHelper;
use Bx\Model\Traits\SortableHelper;
use BX\Router\Exceptions\ServerErrorException;
use CFile;
use Closure;
use Psr\Http\Message\UploadedFileInterface;
use Exception;

class FileService extends BaseModelService
{
    use FilterableHelper;
    use SortableHelper;
    use LimiterHelper;

    static protected function getFilterFields(): array
    {
        return [
            'name' => 'ORIGINAL_NAME',
            'id' => 'ID',
            'type' => 'CONTENT_TYPE',
            'size' => 'FILE_SIZE',
            'height' => 'HEIGHT',
            'width' => 'WIDTH',
        ];
    }

    static protected function getSortFields(): array
    {
        return [
            'name' => 'ORIGINAL_NAME',
            'id' => 'ID',
            'size' => 'FILE_SIZE',
            'height' => 'HEIGHT',
            'width' => 'WIDTH',
        ];
    }

    /**
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return File[]|ModelCollection
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList(array $params, UserContextInterface $userContext = null): ModelCollection
    {
        if ($this->validateFn instanceof Closure) {
            $params = $this->validateFn($params, $userContext);
        }

        $fileList = FileTable::getList($params)->fetchAll();
        return new ModelCollection($fileList, File::class);
    }

    /**
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getCount(array $params, UserContextInterface $userContext = null): int
    {
        if ($this->validateFn instanceof Closure) {
            $params = $this->validateFn($params, $userContext);
        }

        $params['count_total'] = true;
        return FileTable::getList($params)->getCount();
    }

    /**
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return File|AbsOptimizedModel|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getById(int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel
    {
        $params = [
            'filter' => [
                '=ID' => $id
            ],
            'limit' => 1,
        ];

        $fileList = $this->getList($params, $userContext);

        return $fileList->first();
    }

    /**
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return Result
     * @throws Exception
     */
    public function delete(int $id, UserContextInterface $userContext = null): Result
    {
        $file = $this->getById($id, $userContext);
        if (!$file) {
            $result = new Result();
            return $result->addError(new Error('Файл не найден', 404));
        }

        return FileTable::delete($id);
    }

    /**
     * @param AbsOptimizedModel $model
     * @param UserContextInterface|null $userContext
     * @return Result
     * @throws ServerErrorException
     */
    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        throw new ServerErrorException('Not implemented');
    }

    /**
     * @param string $baseDir
     * @param UploadedFileInterface ...$files
     * @return File[]|ModelCollection
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function saveUploadFiles(string $baseDir, UploadedFileInterface ...$files): ModelCollection
    {
        $fileIdList = [];
        foreach ($files as $file) {
            $data = [
                'name' => $file->getClientFilename(),
                'size' => $file->getSize(),
                'tmp_name' => $file->getStream()->getMetadata('uri'),
                'type' => $file->getClientMediaType(),
                'MODULE_ID' => 'bx.model',
            ];

            $fileIdList[] = (int) CFile::SaveFile($data, $baseDir);
        }

        if (empty($fileIdList)) {
            return new ModelCollection([], File::class);
        }

        return $this->getList([
            '=ID' => $fileIdList
        ]);
    }
}