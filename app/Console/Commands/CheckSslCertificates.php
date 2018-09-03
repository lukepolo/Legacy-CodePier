<?php

namespace App\Console\Commands;

use App\Models\Site\Site;
use App\Notifications\SslCertExpiring;
use App\Notifications\SslCertInvalid;
use Illuminate\Console\Command;
use Spatie\SslCertificate\Exceptions\CouldNotDownloadCertificate;
use Spatie\SslCertificate\SslCertificate;

class CheckSslCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:ssl {siteId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = Site::findOrFail($this->argument('siteId'));
        if ($site->domain !== 'default') {
            try {
                $certificate = SslCertificate::createForHostName($site->domain);
            } catch (CouldNotDownloadCertificate $certificate) {
                $site->notify(new SslCertInvalid());
                return;
            }

            if (! $certificate->isValid()) {
                $site->notify(new SslCertInvalid($certificate));
                return;
            }
            if ($certificate->daysUntilExpirationDate() < 7) {
                $site->notify(new SslCertExpiring($certificate));
                return;
            }
        }
    }
}
