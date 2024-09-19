<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function Index(){
        
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
                        'color', 'green',       -- static value or you can set based on logic
                        'isEditable', true,     -- static value or you can set based on logic
                        'id', id
                    ) AS result
                    FROM trainings
                    ORDER BY date_from DESC;");
        $formattedTrainings = array_map(fn($row) => json_decode($row->result, true), $trainings);

        $permissions = auth()->user()->getAllPermissions()->pluck('name');

        return Inertia::render('Dashboard', [
            'participants' => DB::table('participant_lists_view')->groupBy('full_name', 'is_internal')->get(),
            'earliest_day_training' => DB::table('event'),
            'plByMonth' => $results,
            'events' => $formattedTrainings,
            'permissions' => $permissions
        ]);
    }
}
