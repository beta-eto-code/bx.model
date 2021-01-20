<?php


namespace Bx\Model\Services;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\Security\Password;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\BaseModelService;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Models\User;
use Bx\Model\Traits\FilterableHelper;
use Bx\Model\Traits\LimiterHelper;
use Bx\Model\Traits\SortableHelper;
use Bx\Model\UserContext;
use Closure;
use CUser;

class UserService extends BaseModelService implements UserServiceInterface
{
    use FilterableHelper;
    use SortableHelper;
    use LimiterHelper;


    static protected function getFilterFields(): array
    {
        return [
            'id' => 'ID',
            'name' => 'NAME',
            'last_name' => 'LAST_NAME',
            'second_name' => 'SECOND_NAME',
            'email' => 'EMAIL',
            'phone' => 'PERSONAL_PHONE',
        ];
    }

    static protected function getSortFields(): array
    {
        return [
            'id' => 'ID',
            'name' => 'NAME',
            'last_name' => 'LAST_NAME',
            'second_name' => 'SECOND_NAME',
            'email' => 'EMAIL',
            'phone' => 'PERSONAL_PHONE',
        ];
    }

    /**
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return User[]|ModelCollection
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList(array $params, UserContextInterface $userContext = null): ModelCollection
    {
        if ($this->validateFn instanceof Closure) {
            $params = $this->validateFn($params, $userContext);
        }

        $userList = UserTable::getList($params)->fetchAll();
        return new ModelCollection($userList, User::class);
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
        return UserTable::getList($params)->getCount();
    }

    /**
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return User|AbsOptimizedModel|null
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

    public function delete(int $id, UserContextInterface $userContext = null): Result
    {
        $result = new Result();
        $file = $this->getById($id, $userContext);
        if (!$file) {
            return $result->addError(new Error('Пользователь не найден', 404));
        }

        $isSuccess = (bool)CUser::Delete($id);
        if (!$isSuccess) {
            return $result->addError(new Error('Ошибка удаления пользователя', 500));
        }

        return $result;
    }

    /**
     * @param User|AbsOptimizedModel $model
     * @param UserContextInterface|null $userContext
     * @return Result
     */
    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        $result = new Result();
        $cUser = new CUser();
        $data = [
            'NAME' => $model->getName(),
            'LAST_NAME' => $model->getLastName(),
            'SECOND_NAME' => $model->getSecondName(),
            'EMAIL' => $model->getEmail(),
            'PERSONAL_PHONE' => $model->getPhone(),
        ];

        if ($model->getId() > 0) {
            $isSuccess = (bool)$cUser->Update($model->getId(), $data);
            if (!$isSuccess) {
                return $result->addError(new Error('Ошибка обновления пользователя'));
            }

            return $result;
        }

        $id = (int)$cUser->Add($data);
        if (!$id) {
            return $result->addError(new Error('Ошибка добавления пользователя'));
        }

        $model['ID'] = $id;

        return $result;
    }

    /**
     * @param string $login
     * @param string $password
     * @return UserContextInterface|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function login(string $login, string $password): ?UserContextInterface
    {
        $userData = UserTable::getRow([
            'filter' => [
                '=LOGIN' => $login
            ],
            'select' => [
                'ID',
                'PASSWORD'
            ],
            'limit' => 1,
        ]);

        if (empty($userData)) {
            return null;
        }

        if (!Password::equals($userData['PASSWORD'], $password)) {
            return null;
        }

        $user = $this->getById((int)$userData['ID']);
        if (!($user instanceof User)) {
            return null;
        }

        return new UserContext($user);
    }
}