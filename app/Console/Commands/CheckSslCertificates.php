<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Site\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Notifications\SslCertInvalid;
use App\Notifications\SslCertExpiring;
use Spatie\SslCertificate\SslCertificate;
use Spatie\SslCertificate\Exceptions\CouldNotDownloadCertificate;

class CheckSslCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:ssl';

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
        $siteModel = new Site();
        DB::table('sites')
            ->selectRaw('sites.id, sites.domain, sites.user_id, sites.name')
            ->join('users', function ($join) {
                $join->on('users.id', 'sites.user_id');
            })
            ->join('subscriptions', function ($join) {
                $join->on('subscriptions.user_id', 'users.id')
                    ->whereNull('subscriptions.ends_at')
                    ->orWhere('subscriptions.ends_at', '>', Carbon::now())
                ->orWhere('users.role', 'admin');
            })
            ->join('sslCertificateables', function ($join) {
                $join->on('sslCertificateables.sslCertificateable_id', 'sites.id')
                    ->where('sslCertificateables.sslCertificateable_type', Site::class);
            })
            ->join('ssl_certificates', function ($join) {
                $join->on('ssl_certificates.id', 'sslCertificateables.ssl_certificate_id')
                    ->where('ssl_certificates.active', true);
            })
            ->groupBy('sites.id')
            ->orderBy('sites.id')
            ->whereNull('sites.deleted_at')
            ->where('sites.domain', '!=', 'default')
            ->chunk(1000, function ($sites) use ($siteModel) {
                foreach ($sites as $site) {
                    $siteModel->id = $site->id;
                    $siteModel->fill((array) $site);
                    try {
                        $certificate = SslCertificate::createForHostName($site->domain);
                    } catch (CouldNotDownloadCertificate $certificate) {
                        $siteModel->notify(new SslCertInvalid());
                        continue;
                    }

                    if (! $certificate->isValid()) {
                        $siteModel->notify(new SslCertInvalid($certificate));
                        continue;
                    }
                    if ($certificate->daysUntilExpirationDate() < 7) {
                        $siteModel->notify(new SslCertExpiring($certificate));
                        continue;
                    }
                }
            });
    }
}
