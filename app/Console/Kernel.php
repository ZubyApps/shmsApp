<?php

namespace App\Console;

use App\InvokableObjects\CloseNursesShift;
use App\InvokableObjects\NursesAfternoonShift;
use App\InvokableObjects\NursesMorningShift;
use App\InvokableObjects\NursesNightShift;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(new NursesMorningShift)->dailyAt('08:00');
        $schedule->call(new NursesAfternoonShift)->dailyAt('14:00');
        $schedule->call(new NursesNightShift)->dailyAt('20:00');

        $schedule->call(function() {
            Log::info("this scheduler is running");
        })->daily('05:32:02');
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
