<?php

namespace App\Console;

use App\InvokableObjects\NursesAfternoonShift;
use App\InvokableObjects\NurseShifts;
use App\InvokableObjects\NursesMorningShift;
use App\InvokableObjects\NursesNightShift;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(new NursesMorningShift)->dailyAt('15:48');
        $schedule->call(new NursesAfternoonShift)->dailyAt('15:49');
        $schedule->call(new NursesNightShift)->dailyAt('15:50');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
