<?php

namespace App\Http\Controllers\ExternalUsers;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
class DashboardController extends Controller
{
    public function Index(){

        $user = auth()->user();

        // Redirect if the user does not have the "guest" role
        if (!$user->hasRole('guest')) {
            return redirect('/dashboard');
        }

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
                    trainings t, 
                    (
                    SELECT training_id FROM training_participants WHERE email = '".auth()->user()->email."'
                    GROUP BY training_id
                    ) tp
                    WHERE t.id = tp.training_id
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
                    FROM trainings t,
                    (
                    SELECT training_id FROM training_participants WHERE email = '".auth()->user()->email."'
                    GROUP BY training_id
                    ) tp
                    WHERE t.id = tp.training_id
                    ORDER BY date_from DESC;");
        $formattedTrainings = array_map(fn($row) => json_decode($row->result, true), $trainings);

        return Inertia::render('ExternalUserDashboard', [
            'plByMonth' => $results,
            'events' => $formattedTrainings,
        ]);
    }
}
