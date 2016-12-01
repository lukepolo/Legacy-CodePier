<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\Server\ServerCronJob;
use App\Models\Server\ServerFirewallRule;
use App\Models\Server\ServerSshKey;
use App\Models\Server\ServerSslCertificate;
use App\Models\Server\ServerWorker;
use App\Models\Site\SiteCronJob;
use App\Models\Site\SiteDeployment;
use App\Models\Site\SiteFile;
use App\Models\Site\SiteFirewallRule;
use App\Models\Site\SiteSshKey;
use App\Models\Site\SiteSslCertificate;
use App\Models\Site\SiteWorker;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    const COMMANDS = 'commands';
    const SITE_DEPLOYMENTS = 'site_deployments';

    const DEFAULT_TYPES = [
        self::COMMANDS => [
            ServerWorker::class,
            ServerSshKey::class,
            ServerCronJob::class,
            ServerFirewallRule::class,
            ServerSslCertificate::class,

            SiteFile::class,
            SiteWorker::class,
            SiteSshKey::class,
            SiteCronJob::class,
            SiteFirewallRule::class,
            SiteSslCertificate::class,
        ],
        self::SITE_DEPLOYMENTS => [
            SiteDeployment::class,
        ],
    ];
    const PER_PAGE = 10;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $types = collect($request->get('types', self::DEFAULT_TYPES));

        $piles = $request->get('piles', []);
        $sites = $request->get('sites', []);
        $servers = $request->get('servers', []);

        $queryTypes = collect([
            self::SITE_DEPLOYMENTS => DB::table('site_deployments')
                ->select(['site_deployments.id', 'site_deployments.created_at', DB::raw('"'.self::SITE_DEPLOYMENTS.'" as type')])
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
            self::COMMANDS => DB::table('commands')
                ->select(['commands.id', 'commands.created_at', DB::raw('"'.self::COMMANDS.'" as type')])
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
        ])->only($types->keys()->toArray());

        /** @var Builder $combinedQuery */
        $combinedQuery = $queryTypes->shift();

        foreach ($queryTypes as $type => $query) {
            $combinedQuery->unionAll($query);
        }

        $tempCombinedQuery = clone $combinedQuery;
        $topResults = $combinedQuery->latest()->offset($request->get('page', 1) * self::PER_PAGE)->take(self::PER_PAGE)->get();

        return response()->json(
            $this->getPaginatedObject(
                $tempCombinedQuery,
                collect([
                    self::SITE_DEPLOYMENTS => SiteDeployment::with([
                            'serverDeployments.server',
                            'serverDeployments.events.step' => function ($query) {
                                $query->withTrashed();
                            },
                            'site.pile',
                            'site.userRepositoryProvider.repositoryProvider',
                        ])
                        ->whereIn('id', $topResults->filter(function ($event) {
                            return $event->type == self::SITE_DEPLOYMENTS;
                        })->keyBy('id')->keys()),
                    self::COMMANDS => Command::with([
                            'site.pile',
                            'commandable',
                            'serverCommands.server',
                        ])
                        ->whereIn(
                        'id', $topResults->filter(function ($event) {
                            return $event->type == self::COMMANDS;
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
                ->selectRaw('count(id) as total FROM ('.$combinedQuery->toSql().') as total')
                ->setBindings($combinedQuery->getBindings())
                ->first()->total,
            $perPage,
            $currentPage);
    }
}
