<?php

namespace App\Modules\Auth\Observers;

use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Класс наблюдатель за моделбю User.
 *
 * Class UserObserver
 * @package App\Modules\Auth\Observers
 */
class UserObserver
{
    /**
     * Обработка события перед сохранением.
     *
     * @param $user
     */
    public function creating($user): void
    {
        $user->status = User::STATUS_NEW;
        $user->password = Hash::make($user->password);
    }
}
