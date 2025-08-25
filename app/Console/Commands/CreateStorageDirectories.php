<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CreateStorageDirectories extends Command
{
    protected $signature = 'storage:directories';
    protected $description = 'Create all required storage directories';

    public function handle()
    {
        $disks = config('filesystems.disks');

        foreach ($disks as $diskName => $config) {
            $this->info("Processing disk: {$diskName}");

            // Get required directories from config or use default
            $directories = $config['directories']['required'] ?? [];

            foreach ($directories as $directory) {
                $fullPath = Storage::disk($diskName)->path($directory);

                if (!File::exists($fullPath)) {
                    File::makeDirectory($fullPath, 0755, true);
                    $this->line("Created directory: {$fullPath}");
                } else {
                    $this->line("Directory exists: {$fullPath}");
                }
            }
        }

        $this->info('All directories processed!');
    }
}
