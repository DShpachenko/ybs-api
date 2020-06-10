<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Auth\Models\User;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserRepository
 * @package App\Modules\Auth\Repositories
 */
class UserRepository extends Repository
{
    /**
     * @return mixed|string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Приведение номера к единой форме (только числа).
     *
     * @param $phone
     * @return int|null
     */
    public static function clearPhoneNumber($phone): ? int
    {
        if ($phone === '' || !$phone) {
            return null;
        }

        return preg_replace('/[\D]/', '', $phone);
    }

    /**
     * Поиск пользователя по номеру телефона.
     *
     * @param $phone
     * @param $statuses
     * @return User|null
     */
    public function findByPhone($phone, $statuses = []): ? User
    {
        return $this->model->where('phone', self::clearPhoneNumber($phone))
            ->whereIn('status', $statuses)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Создание не подтвержденного пользователя.
     *
     * @param $data
     * @return int|null
     */
    public function registrationNewUser($data): ? int
    {
        if ($user = $this->create($data)) {
            return $user->id;
        }

        return null;
    }

    /**
     * Присвоение статуса подтвержденного пользователя.
     *
     * @param User $user
     */
    public function confirmUser($user): void
    {
        $user->status = User::STATUS_CONFIRMED;
        $user->save();
    }

    /**
     * Обновление пользовательского пароля.
     *
     * @param $user
     * @param $password
     */
    public function updatePassword($user, $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }
}
