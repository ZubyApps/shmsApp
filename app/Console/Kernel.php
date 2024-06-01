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

        $schedule->call(new NursesMorningShift)->timezone('Africa/Lagos')->dailyAt('08:00');
        $schedule->call(new NursesAfternoonShift)->timezone('Africa/Lagos')->dailyAt('14:00');
        $schedule->call(new NursesNightShift)->timezone('Africa/Lagos')->dailyAt('20:00');
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
