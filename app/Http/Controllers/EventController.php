<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\Bitt;
use App\Models\Buoy;
use App\Models\File;
use App\Models\Daemon;
use App\Models\Schema;
use App\Models\SshKey;
use App\Models\Worker;
use App\Models\Command;
use App\Models\CronJob;
use App\Models\Site\Site;
use App\Models\SchemaUser;
use App\Models\FirewallRule;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Models\SslCertificate;
use App\Models\LanguageSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\EnvironmentVariable;
use App\Models\Site\SiteDeployment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class EventController extends Controller
{
    const COMMANDS = 'commands';
    const SITE_DEPLOYMENTS = 'site_deployments';
    const SERVER_PROVISIONING = 'server_provisioning';

    const DEFAULT_TYPES = [
        self::COMMANDS => [
            Site::class,
            Bitt::class,
            Buoy::class,
            File::class,
//            Backup::class, // TODO - this is a bit tricky cause it only adds after relation and not based on the site
            Daemon::class,
            Worker::class,
            SshKey::class,
            Schema::class,
            Server::class,
            CronJob::class,
            SchemaUser::class,
            SchemaUser::class,
            FirewallRule::class,
            SslCertificate::class,
            LanguageSetting::class,
            EnvironmentVariable::class,
        ],
        self::SITE_DEPLOYMENTS => [
            SiteDeployment::class,
        ],
        self::SERVER_PROVISIONING => [
            Server::class,
        ],
    ];

    const PER_PAGE = 20;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $types = collect($request->get('events', self::DEFAULT_TYPES));

        $piles = $request->get('piles', []);
        $sites = $request->get('sites', []);
        $servers = $request->get('servers', []);

        $queryTypes = collect([
            self::SITE_DEPLOYMENTS => SiteDeployment::select(['site_deployments.id', 'site_deployments.created_at', DB::raw('"'.self::SITE_DEPLOYMENTS.'" as type')])
                    ->when($piles, function (Builder $query) use ($piles) {
                        return $query->join('sites', 'site_deployments.site_id', '=', 'sites.id')
                            ->whereIn('sites.pile_id', $piles);
                    })
                    ->when($sites, function (Builder $query) use ($sites) {
                        return $query->whereIn('site_deployments.site_id', $sites);
                    })
                    ->when($servers, function (Builder $query) use ($servers) {
                        return $query->join('sites', 'site_deployments.site_id', '=', 'sites.id')
                            ->join('server_site', 'sites.id', '=', 'server_site.site_id')
                            ->whereIn('server_site.server_id', $servers);
                    }),
            self::COMMANDS => Command::select(['commands.id', 'commands.created_at', DB::raw('"'.self::COMMANDS.'" as type')])
                    ->when($piles, function (Builder $query) use ($piles) {
                        return $query->join('sites', 'commands.site_id', '=', 'sites.id')
                            ->whereIn('sites.pile_id', $piles);
                    })
                    ->when($sites, function (Builder $query) use ($sites) {
                        return $query->whereIn('commands.site_id', $sites);
                    })
                    ->when($servers, function (Builder $query) use ($servers) {
                        return $query->join('server_commands', 'server_commands.command_id', '=', 'commands.id')
                            ->whereIn('server_commands.server_id', $servers);
                    })
                    ->when($types->has('commands'), function (Builder $query) use ($types) {
                        return $query->whereIn('commands.commandable_type', $types->get('commands'));
                    }),
            self::SERVER_PROVISIONING => Server::whereHas('provisionSteps')->select(['servers.id', 'servers.created_at', DB::raw('"'.self::SERVER_PROVISIONING.'" as type')]),
        ])->only($types->keys()->toArray());

        /** @var Builder $combinedQuery */
        $combinedQuery = $queryTypes->shift();

        foreach ($queryTypes as $type => $query) {
            $combinedQuery->unionAll($query);
        }

        $tempCombinedQuery = clone $combinedQuery;

        $topResults = $combinedQuery->latest('created_at')
            ->offset(($request->get('page', 1) - 1) * self::PER_PAGE)
            ->take(self::PER_PAGE)
            ->get();

        return response()->json(
            $this->getPaginatedObject(
                $tempCombinedQuery,
                collect([
                    self::SITE_DEPLOYMENTS => SiteDeployment::with([
                            'serverDeployments.events.step' => function ($query) {
                                $query->withTrashed();
                            },
                        ])
                        ->whereIn('id', $topResults->filter(function ($event) {
                            return $event->type == self::SITE_DEPLOYMENTS;
                        })->keyBy('id')->keys()),
                    self::COMMANDS => Command::with([
                            'serverCommands.server',
                        ])
                        ->whereIn(
                        'id', $topResults->filter(function ($event) {
                            return $event->type == self::COMMANDS;
                        })->keyBy('id')->keys()),
                    self::SERVER_PROVISIONING => Server::with(['provisionSteps'])
                        ->whereIn('id', $topResults->filter(function ($event) {
                            return $event->type == self::SERVER_PROVISIONING;
                        })->keyBy('id')->keys()),
                ])->only($types->keys()->toArray())->map(function ($query) {
                    return $query->get();
                })
                ->flatten()
                ->sortByDesc('created_at'),
            $request->get('page')
        ));
    }

    /**
     * @param Builder $combinedQuery
     * @param Collection $items
     * @param int $currentPage
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    private function getPaginatedObject(Builder $combinedQuery, Collection $items, $currentPage = 1, $perPage = self::PER_PAGE)
    {
        return new LengthAwarePaginator(
            $items->values()->all(),
            DB::query()
                ->selectRaw('count(*) as total FROM ('.$combinedQuery->toSql().') as total')
                ->setBindings($combinedQuery->getBindings())
                ->first()->total,
            $perPage,
            $currentPage
        );
    }
}
