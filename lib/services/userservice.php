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
     * @param User $model
     * @param UserContextInterface|null $userContext
     * @return Result
     */
    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        $data = [
            'NAME' => $model->getName(),
            'LAST_NAME' => $model->getLastName(),
            'SECOND_NAME' => $model->getSecondName(),
            'EMAIL' => $model->getEmail(),
            'PERSONAL_PHONE' => $model->getPhone(),
        ];

        return $this->saveUserData($model, $data);
    }

    /**
     * @param User $model
     * @param array $data
     * @return Result
     */
    private function saveUserData(User $model, array $data): Result
    {
        $result = new Result();
        $cUser = new CUser();
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

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        global $USER;
        return (bool)($USER->IsAuthorized() ?? false);
    }

    /**
     * @return UserContextInterface|null
     */
    public function getCurrentUser(): ?UserContextInterface
    {
        global $USER;
        $userId = (int)$USER->GetID();
        if (!$userId) {
            return null;
        }

        $user = $this->getById($userId);
        if (!($user instanceof User)) {
            return null;
        }

        return new UserContext($user);
    }

    /**
     * @param User $user
     * @param string ...$keyListForSave
     * @return void
     */
    public function saveExtendedData(User $user, string ...$keyListForSave): Result
    {
        $data = [];
        if (!empty($keyListForSave)) {
            foreach($keyListForSave as $key) {
                if ($user->hasValueKey($key)) {
                    $data[$key] = $user->getValueByKey($key);
                }
            }

            return $this->saveUserData($user, $data);
        }

        foreach($user as $key => $value) {
            if (!in_array($key, ['PASSWORD', 'CHECKWORD'])) {
                $data[$key] = $value;
            }
        }

        return $this->saveUserData($user, $data);
    }

    /**
     * @param User $user
     * @param string $password
     * @return Result
     */
    public function updatePassword(User $user, string $password): Result
    {
        if (!$user->getId()) {
            return (new Result())->addError(new Error('Invalid user'));
        }

        $user['PASSWORD'] = $password;
        $result = $this->saveExtendedData($user, 'PASSWORD');
        unset($user['PASSWORD']);

        return $result;
    }
}
