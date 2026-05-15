<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MigrateSidFilesCommand extends Command
{
    protected $signature = 'izinsakit:migrate-sid-files';

    protected $description = 'Pindahkan file SID dari lokasi lama ke storage/app/public/uploads/sid';

    public function handle(): int
    {
        $targetDir = Storage::disk('public')->path('uploads/sid');

        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0775, true);
        }

        $legacyDirs = [
            storage_path('app/public/public/uploads/sid'),
            storage_path('app/public/uploads/sid'),
            public_path('storage/public/uploads/sid'),
            public_path('storage/uploads/sid'),
        ];

        $moved = 0;

        foreach ($legacyDirs as $legacyDir) {
            if (!File::isDirectory($legacyDir)) {
                continue;
            }

            foreach (File::files($legacyDir) as $file) {
                $filename = $file->getFilename();
                $destination = $targetDir . DIRECTORY_SEPARATOR . $filename;

                if (File::exists($destination)) {
                    $this->line("Lewati (sudah ada): {$filename}");
                    continue;
                }

                File::copy($file->getPathname(), $destination);
                $this->info("Dipindahkan: {$filename}");
                $moved++;
            }
        }

        $this->info("Selesai. {$moved} file dipindahkan ke uploads/sid.");

        return self::SUCCESS;
    }
}
