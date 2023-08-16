<?php


namespace App\Repositories;

use App\Models\Group as Model;


class GroupRepository extends CoreRepository
{
    public function getModelClass()
    {
        return Model::class;
    }

    public function getGroupIdForName($name)
    {
        $row = $this->startCondition()
            ->where('name', $name)
            ->first();

        return (!empty($row)) ? $row->id : null;
    }

    public function gatNamesGroup($groupsId)
    {
        $rows = $this->startCondition()
            ->select('id', 'name')
            ->whereIn('id', $groupsId)
            ->get()
            ->pluck('name')
            ->toArray();

        return !empty($rows) ? implode(', ', $rows) : null;
    }
}
