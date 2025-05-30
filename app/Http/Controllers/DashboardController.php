<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\EvaluationTraining;

class DashboardController extends Controller
{
    public function Index()
    {
        // Existing queries
        $results = DB::select("SELECT
                month,
                mname,
                yr,
                total_trainings
                FROM (
                    SELECT
                    DATE_FORMAT(date_from, '%Y-%m') AS month, 
                    MONTHNAME(date_from) AS mname,
                    YEAR(date_from) AS yr,
                    COUNT(*) AS total_trainings
                    FROM
                    trainings
                    GROUP BY
                    YEAR(date_from),
                    DATE_FORMAT(date_from, '%Y-%m'),
                    MONTHNAME(date_from)
                    ORDER BY
                    DATE_FORMAT(date_from, '%Y-%m') DESC
                    LIMIT 12
                ) AS subquery
                ORDER BY
                month ASC
            ");

        $trainings = DB::select("SELECT JSON_OBJECT(
                        'title', title,
                        'location', venue,
                        'time', JSON_OBJECT('start', date_from, 'end', date_to),
                        'color', 'green',
                        'isEditable', true,
                        'id', id
                    ) AS result
                    FROM trainings
                    ORDER BY date_from DESC;");

        $formattedTrainings = array_map(fn($row) => json_decode($row->result, true), $trainings);

        $permissions = auth()->user()->getAllPermissions()->pluck('name');

        // New summaries:
        $totalTrainings = DB::table('trainings')->count();

        $totalParticipants = DB::table('participant_lists_view')->count();

        $internalParticipants = DB::table('participant_lists_view')->where('is_internal', true)->count();
        $externalParticipants = DB::table('participant_lists_view')->where('is_internal', false)->count();

        $upcomingTrainingsCount = DB::table('trainings')->where('date_from', '>=', now())->count();

    $latestTraining = DB::table('trainings')
                    ->orderBy('date_from', 'desc')
                    ->select('title', 'date_from')
                    ->limit(10)
                    ->get();

        // Average trainings per month (last 12 months)
        $averageTrainingsPerMonth = round(collect($results)->avg('total_trainings'), 2);

        $officeRepSummary = DB::table('evaluation_trainings as et')
    ->join('office_representatives as or', 'et.office_rep_id', '=', 'or.id')
    ->select('or.title as office_rep_title', DB::raw('COUNT(et.id) as total_evaluations'))
    ->groupBy('or.title')
    ->orderBy('total_evaluations', 'desc')
    ->get();

        return Inertia::render('Dashboard', [
            'participants' => DB::table('participant_lists_view')->groupBy('full_name', 'is_internal')->get(),
            'plByMonth' => $results,
            'events' => $formattedTrainings,
            'permissions' => $permissions,

            // Send new summary data
            'totalTrainings' => $totalTrainings,
            'totalParticipants' => $totalParticipants,
            'internalParticipants' => $internalParticipants,
            'externalParticipants' => $externalParticipants,
            'upcomingTrainingsCount' => $upcomingTrainingsCount,
            'latestTraining' => $latestTraining,
            'averageTrainingsPerMonth' => $averageTrainingsPerMonth,
            'officeRepSummary' => $officeRepSummary
        ]);
    }

}
