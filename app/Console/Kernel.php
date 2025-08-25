<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\CreateStorageDirectories::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Your scheduled commands
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
