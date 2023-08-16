<?php


namespace App\Repositories;

use App\Models\User as Model;


class UserRepository extends CoreRepository
{
    public function getModelClass()
    {
        return Model::class;
    }

    public function getUserIdForNameOrEmail($nameOrEmail)
    {
        $row = $this->startCondition()
            ->where('name', $nameOrEmail)
            ->orWhere('email', $nameOrEmail)
            ->first();

        return (!empty($row)) ? $row->id : null;
    }

    public function getExpiredGroups()
    {
        $rows = $this->startCondition()
            ->whereHas('expiredGroups')
            ->with('expiredGroups')
            ->get();

        if($rows->count() > 0) {
            $rows->map(function ($user) {
                $user->expiredGroups = $user->expiredGroups->modelKeys();
                return $user;
            });
        }

        return $rows;
    }
}
