<?php

namespace App\Console;

use App\InvokableObjects\CleanUpTables;
use App\InvokableObjects\MedicationsForSms;
use App\InvokableObjects\NursesAfternoonShift;
use App\InvokableObjects\NursesMorningShift;
use App\InvokableObjects\NursesNightShift;
use App\InvokableObjects\Remind;
use App\Notifications\MedicationNotifier;
use App\Services\ChurchPlusSmsService;
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
        $schedule->call(new NursesMorningShift)->timezone('Africa/Lagos')->dailyAt('08:00');
        $schedule->call(new NursesAfternoonShift)->timezone('Africa/Lagos')->dailyAt('14:00');
        $schedule->call(new NursesNightShift)->timezone('Africa/Lagos')->dailyAt('19:30');
        $schedule->call(new Remind)->timezone('Africa/Lagos')->twiceDaily(9, 15);
        $schedule->call(new CleanUpTables)->timezone('Africa/Lagos')->lastDayOfMonth('23:59');
        $schedule->call(new MedicationsForSms(new MedicationNotifier(new ChurchPlusSmsService())))->timezone('Africa/Lagos')->between('08:00', '20:00');
        Log::info('Ran');
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
