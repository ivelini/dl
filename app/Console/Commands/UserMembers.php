<?php

namespace App\Console\Commands;

use App\Repositories\CoreRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UserMembers extends Command
{
    private $userRepository;
    private  $groupRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->groupRepository = new GroupRepository();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:member {value* : Входные данные. При get=uid - почта либо имя пользователя. При get=gid имя группы. При add - id пользователя, id группы.}
                            {--get=uid : Get uid - User ID, gui - Group ID}
                            {--add : Add User ID in Group ID} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Данные о пользователе или группе';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $value = $this->argument('value');

        if ($this->option('add')) {
            $userId = $value[0];
            $groupId = $value[1];

            $user = $this->userRepository->getModel($userId);
            $group = $this->groupRepository->getModel($groupId);

            if(!empty($user) && $user->groups()->where('id', $groupId)->first() == NULL) {

                $user->groups()->attach($groupId);

                $user->active = true;
                $user->save();

            } else {
                $this->info('Пользователь уже добавлен');
            }

        } elseif ($this->option('get') == 'uid') {

            $this->info('User ID: ' . $this->userRepository->getUserIdForNameOrEmail(current($value)));

        } elseif ($this->option('get') == 'gid') {

            $this->info('Group ID: ' . $this->groupRepository->getGroupIdForName(current($value)));
        }
    }
}
