<?php

namespace App\Console;

use App\Jobs\RappelRelance;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \Laravelista\LumenVendorPublish\VendorPublishCommand::class, // Pour pouvoir publier le Vendor
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new RappelRelance)
                 ->dailyAt('11:00')
                 ->withoutOverlapping()
                 ->thenPing('https://cronhub.io/ping/48a498e0-58b2-11e9-b19b-2197cd63b9eb');
    }
}
