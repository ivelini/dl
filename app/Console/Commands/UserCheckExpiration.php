<?php

namespace App\Console\Commands;


use App\Jobs\UserDeactivateJob;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendUserExcludeGroup;

class UserCheckExpiration extends Command
{
    private $userRepository;
    private  $groupRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check_expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка времени нахождения пользователя в группе';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usersIncludeExpiredGroups = $this->userRepository->getExpiredGroups();

        if($usersIncludeExpiredGroups->count() > 0) {

            foreach ($usersIncludeExpiredGroups as $user) {

                $user->groups()->detach($user->expiredGroups);

                Mail::send(new SendUserExcludeGroup($user, $user->expiredGroups));

                UserDeactivateJob::dispatch($user);
            }
        }
    }
}
