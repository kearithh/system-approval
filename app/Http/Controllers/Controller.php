<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var User
     */
    public $ceo;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
//        $this->ceo = $this->getCEO();
        $totalPendingRequest = DB::table('requests')
            ->where('requests.user_id', Auth::id())
            ->whereBetween('requests.status', [0, 99])
            ->where('draft', '=', 0)
            ->count('*');

//        dump($totalPendingRequest,  auth()->id());

        $positionId = 0;
        if (Auth::user()) {
            $positionId = Auth::user()->position_id;
        }

        $totalPendingReview = DB::table('requests')
            ->join('users', 'users.id', '=', 'requests.created_by')
            ->join('approve', 'users.position_id', '=', 'approve.reviewer_position_id')
            ->where('approve.reviewer_position_id', $positionId)
            ->whereBetween('requests.status', [0, 49])
            ->count('*');

        View::share(compact('totalPendingRequest', 'totalPendingReview'));
    }

//    /**
//     * @return mixed
//     */
//    public function getCEO()
//    {
//        $ceo = User
//            ::join('positions', 'users.position_id', '=', 'positions.id')
//            ->where('positions.level', config('app.position_level_president'))->first();
//        return $ceo;
//    }
}
