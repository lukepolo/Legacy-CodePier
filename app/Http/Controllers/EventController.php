<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\Site\SiteDeployment;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    const COMMANDS = 'commands';
    const SITE_DEPLOYMENTS = 'site_deployments';

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $types = $request->get('types', [
            self::COMMANDS,
            self::SITE_DEPLOYMENTS,
        ]);

        $piles = $request->get('piles', []);
        $sites = $request->get('sites', []);
        $servers = $request->get('servers', []);

        $queryTypes = collect([
            'site_deployments' => DB::table('site_deployments')
                ->select(['site_deployments.id', 'site_deployments.created_at', DB::raw('"'.self::SITE_DEPLOYMENTS.'" as type')])
                ->when($piles, function (Builder $query) use ($piles) {
                    return $query->join('sites', 'site_deployments.site_id', '=', 'sites.id')
                        ->whereIn('sites.pile_id', $piles);
                })
                ->when($sites, function (Builder $query) use ($sites) {
                    return $query->whereIn('site_id', $sites);
                })
                ->when($servers, function (Builder $query) use ($servers) {
                    return $query->join('sites', 'site_deployments.site_id', '=', 'sites.id')
                        ->join('server_site', 'sites.id', '=', 'server_site.site_id')
                        ->whereIn('server_site.server_id', $servers);
                }),
            'commands' => DB::table('commands')
                ->select(['commands.id', 'commands.created_at', DB::raw('"'.self::COMMANDS.'" as type')])
                ->when($piles, function (Builder $query) use ($piles) {
                    return $query->join('sites', 'commands.site_id', '=', 'sites.id')
                        ->whereIn('sites.pile_id', $piles);
                })
                ->when($sites, function (Builder $query) use ($sites) {
                    return $query->whereIn('site_id', $sites);
                })
                ->when($servers, function (Builder $query) use ($servers) {
                    return $query->whereIn('server_id', $servers);
                }),
        ])->only($types);

        /** @var Builder $combinedQuery */
        $combinedQuery = $queryTypes->shift();

        foreach ($queryTypes as $type => $query) {
            $combinedQuery->unionAll($query);
        }

        $tempCombinedQuery = clone $combinedQuery;
        $topResults = $combinedQuery->latest()->take(10)->get();

        return response()->json(
            $this->getPaginatedObject(
                $tempCombinedQuery,
                collect([
                    'site_deployments' => SiteDeployment::with(['serverDeployments.server', 'serverDeployments.events.step' => function ($query) {
                        $query->withTrashed();
                    },
                            'site.pile',
                            'site.userRepositoryProvider.repositoryProvider',
                        ])
                        ->whereIn('id', $topResults->filter(function ($event) {
                            return $event->type == self::SITE_DEPLOYMENTS;
                        })->keyBy('id')->keys()),
                    'commands' => Command::with([
                                'server',
                                'site.pile',
                            ])
                            ->whereIn(
                            'id', $topResults->filter(function ($event) {
                                return $event->type == self::COMMANDS;
                            })->keyBy('id')->keys()),
                ])->only($types)->map(function ($query) {
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
    private function getPaginatedObject(Builder $combinedQuery, Collection $items, $currentPage = 1, $perPage = 25)
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
