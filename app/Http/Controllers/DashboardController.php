<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function Index(Request $request)
    {
        $scope = $request->query('scope', 'all');
        $value = (string) $request->query('value', '');

        if (! in_array($scope, ['all', 'division', 'section', 'training'], true)) {
            $scope = 'all';
            $value = '';
        }

        if ($scope === 'training' && $value !== '' && ! Training::whereKey($value)->exists()) {
            $scope = 'all';
            $value = '';
        }

        if ($scope === 'all') {
            $value = '';
        }

        $trainingIds = $this->resolveTrainingIdsForScope($scope, $value);
        $hasTrainingFilter = $trainingIds !== null;
        $emptyScope = $hasTrainingFilter && $trainingIds->isEmpty();

        $results = $this->monthlyTrainingTotals($trainingIds, $emptyScope);

        $formattedTrainings = DB::table('trainings')
            ->when($hasTrainingFilter, function ($q) use ($emptyScope, $trainingIds) {
                if ($emptyScope) {
                    $q->whereRaw('1 = 0');
                } else {
                    $q->whereIn('id', $trainingIds);
                }
            })
            ->orderByDesc('date_from')
            ->selectRaw("JSON_OBJECT(
                'title', title,
                'location', venue,
                'time', JSON_OBJECT('start', date_from, 'end', date_to),
                'color', 'green',
                'isEditable', true,
                'id', id
            ) AS result")
            ->get()
            ->map(fn ($row) => json_decode($row->result, true))
            ->all();

        $permissions = auth()->user()->getAllPermissions()->pluck('name');

        if ($scope === 'all') {
            $totalTrainings = DB::table('trainings')->count();
            $totalParticipants = DB::table('participant_lists_view')->count();
            $internalParticipants = DB::table('participant_lists_view')->where('is_internal', true)->count();
            $externalParticipants = DB::table('participant_lists_view')->where('is_internal', false)->count();
            $participants = DB::table('participant_lists_view')->groupBy('full_name', 'is_internal')->get();
        } else {
            if ($emptyScope) {
                $totalTrainings = 0;
                $totalParticipants = 0;
                $internalParticipants = 0;
                $externalParticipants = 0;
                $participants = collect();
            } else {
                $totalTrainings = DB::table('trainings')->whereIn('id', $trainingIds)->count();

                $participantAgg = DB::table('training_participants')
                    ->whereIn('training_id', $trainingIds)
                    ->selectRaw('is_internal, CONCAT(lname, \', \', fname) as full_name')
                    ->groupBy('is_internal', DB::raw('CONCAT(lname, \', \', fname)'))
                    ->get();

                $totalParticipants = $participantAgg->count();
                $internalParticipants = $participantAgg->filter(fn ($r) => (bool) $r->is_internal)->count();
                $externalParticipants = $participantAgg->reject(fn ($r) => (bool) $r->is_internal)->count();

                $participants = DB::table('training_participants')
                    ->whereIn('training_id', $trainingIds)
                    ->selectRaw('is_internal, MAX(COALESCE(CAST(is_female AS UNSIGNED), 0)) as is_female, CONCAT(lname, \', \', fname) as full_name')
                    ->groupBy('is_internal', DB::raw('CONCAT(lname, \', \', fname)'))
                    ->get();
            }
        }

        $upcomingBase = DB::table('trainings')->where('date_from', '>=', now());
        if ($hasTrainingFilter) {
            $upcomingBase = $emptyScope
                ? $upcomingBase->whereRaw('1 = 0')
                : $upcomingBase->whereIn('id', $trainingIds);
        }
        $upcomingTrainingsCount = $upcomingBase->count();

        $latestQuery = DB::table('trainings')
            ->when($hasTrainingFilter, function ($q) use ($emptyScope, $trainingIds) {
                if ($emptyScope) {
                    $q->whereRaw('1 = 0');
                } else {
                    $q->whereIn('id', $trainingIds);
                }
            })
            ->orderByDesc('date_from')
            ->select('title', 'date_from')
            ->limit(10);

        $latestTraining = $latestQuery->get();

        $averageTrainingsPerMonth = round(collect($results)->avg('total_trainings') ?? 0, 2);

        $officeRepQuery = DB::table('evaluation_trainings as et')
            ->join('office_representatives as or', 'et.office_rep_id', '=', 'or.id')
            ->select('or.title as office_rep_title', DB::raw('COUNT(et.id) as total_evaluations'))
            ->groupBy('or.title')
            ->orderByDesc('total_evaluations');

        if ($hasTrainingFilter) {
            $officeRepQuery = $emptyScope
                ? $officeRepQuery->whereRaw('1 = 0')
                : $officeRepQuery->whereIn('et.training_id', $trainingIds);
        }

        $officeRepSummary = $officeRepQuery->get();

        return Inertia::render('Dashboard', [
            'participants' => $participants,
            'plByMonth' => $results,
            'events' => $formattedTrainings,
            'permissions' => $permissions,
            'totalTrainings' => $totalTrainings,
            'totalParticipants' => $totalParticipants,
            'internalParticipants' => $internalParticipants,
            'externalParticipants' => $externalParticipants,
            'upcomingTrainingsCount' => $upcomingTrainingsCount,
            'latestTraining' => $latestTraining,
            'averageTrainingsPerMonth' => $averageTrainingsPerMonth,
            'officeRepSummary' => $officeRepSummary,
            'dashboardFilter' => [
                'scope' => $scope,
                'value' => $value,
                'training_label' => ($scope === 'training' && $value !== '')
                    ? Training::whereKey($value)->value('title')
                    : null,
            ],
            'filterOptions' => [
                'divisions' => User::query()
                    ->whereNotNull('division')
                    ->where('division', '!=', '')
                    ->distinct()
                    ->orderBy('division')
                    ->pluck('division')
                    ->values()
                    ->all(),
                'sections' => User::query()
                    ->whereNotNull('section')
                    ->where('section', '!=', '')
                    ->distinct()
                    ->orderBy('section')
                    ->pluck('section')
                    ->values()
                    ->all(),
            ],
        ]);
    }

    /**
     * Paginated, searchable training list for the dashboard filter (JSON).
     * Uses the query builder so Training model appends (e.g. evaluation_status) are not loaded.
     */
    public function searchTrainings(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = min(max((int) $request->query('per_page', 15), 5), 50);

        $query = DB::table('trainings')
            ->whereNull('deleted_at')
            ->select('id', 'title', 'date_from')
            ->orderByDesc('date_from');

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where('title', 'like', $like);
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    private function resolveTrainingIdsForScope(string $scope, string $value): ?Collection
    {
        if ($scope === 'all' || $value === '') {
            return null;
        }

        if ($scope === 'training') {
            return collect([$value]);
        }

        if ($scope === 'division') {
            return DB::table('training_participants as tp')
                ->join('users as u', 'tp.email', '=', 'u.email')
                ->where('tp.is_internal', true)
                ->where('u.division', $value)
                ->distinct()
                ->pluck('tp.training_id');
        }

        if ($scope === 'section') {
            return DB::table('training_participants as tp')
                ->join('users as u', 'tp.email', '=', 'u.email')
                ->where('tp.is_internal', true)
                ->where('u.section', $value)
                ->distinct()
                ->pluck('tp.training_id');
        }

        return null;
    }

    private function monthlyTrainingTotals(?Collection $trainingIds, bool $emptyScope): array
    {
        $inner = DB::table('trainings')
            ->selectRaw("DATE_FORMAT(date_from, '%Y-%m') AS month, MONTHNAME(date_from) AS mname, YEAR(date_from) AS yr, COUNT(*) AS total_trainings")
            ->when($trainingIds !== null, function ($q) use ($trainingIds, $emptyScope) {
                if ($emptyScope) {
                    $q->whereRaw('1 = 0');
                } else {
                    $q->whereIn('id', $trainingIds);
                }
            })
            ->groupByRaw('YEAR(date_from), DATE_FORMAT(date_from, "%Y-%m"), MONTHNAME(date_from)')
            ->orderByRaw('DATE_FORMAT(date_from, "%Y-%m") DESC')
            ->limit(12);

        return DB::query()
            ->fromSub($inner, 'subquery')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }
}
