<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Console\Command;

class GenerateQrCodesForExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:generate-missing {--force : Force regeneration for all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for existing users who don\'t have them';

    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        parent::__construct();
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        if ($force) {
            $users = User::all();
            $this->info('Regenerating QR codes for ALL users...');
        } else {
            $users = User::whereNull('qr_code_data')->get();
            $this->info('Generating QR codes for users without them...');
        }

        if ($users->isEmpty()) {
            $this->info('No users found that need QR codes.');
            return 0;
        }

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        $generated = 0;

        foreach ($users as $user) {
            $qrCodeData = $this->qrCodeService->generateQrCodeData(
                $user->id,
                $user->email,
                $user->username
            );

            $user->update(['qr_code_data' => $qrCodeData]);
            $generated++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully generated QR codes for {$generated} users.");

        return 0;
    }
}
