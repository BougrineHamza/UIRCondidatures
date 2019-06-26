<?php

namespace App\Jobs;

use App\User;
use Carbon\Carbon;
use App\Mail\RappelConcours;
use Illuminate\Support\Facades\Mail;

class RappelRelance extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * entretien: 24-02-2019
     * Aujourdhui: 22-02-2019 +.
     * @return void
     */
    public function handle()
    {
        $users = User::whereHas('concourtime.concourdate', function ($q) {
            $q->whereBetween('date_concours', [Carbon::now()->toDateTimeString(), Carbon::now()->addDays(3)->toDateTimeString()]);
        })->get();

        foreach ($users as $user) {
            Mail::to($user)->send(new RappelConcours($user->email, $user->password));
        }
    }
}
