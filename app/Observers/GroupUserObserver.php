<?php

namespace App\Observers;

use App\Models\Group;
use App\Models\GroupUser;
use Carbon\Carbon;

class GroupUserObserver
{
    /**
     * Handle the GroupUser "creating" event.
     */
    public function creating(GroupUser $groupUser): void
    {
        $group = Group::find($groupUser->group_id);
        $groupUser->expired_at = Carbon::now()->addHours($group->expire_hours);
    }

    /**
     * Handle the GroupUser "updated" event.
     */
    public function updated(GroupUser $groupUser): void
    {
        //
    }

    /**
     * Handle the GroupUser "deleted" event.
     */
    public function deleted(GroupUser $groupUser): void
    {
        //
    }

    /**
     * Handle the GroupUser "restored" event.
     */
    public function restored(GroupUser $groupUser): void
    {
        //
    }

    /**
     * Handle the GroupUser "force deleted" event.
     */
    public function forceDeleted(GroupUser $groupUser): void
    {
        //
    }
}
