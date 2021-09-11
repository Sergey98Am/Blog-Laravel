<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:deleteOldNotifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            $userNotifications = $user->notifications(); // Order by 'id' 'desc'
            $notificationsCount = $userNotifications->count();
            $skip = 150;
            if ($notificationsCount > $skip) {
                $limit = $notificationsCount - $skip;
                $userNotifications->skip($skip)->take($limit)->delete();
            }
        }
    }
}
