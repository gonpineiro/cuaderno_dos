<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{

    public function index()
    {
        return User::with('activities.subject')->get();
    }

    public function get_users()
    {
        $users = User::select('id', 'name')
            ->where('name', '!=', 'sudo')
            ->get();
        return sendResponse($users);
    }

    public function show(Request $request, $id)
    {

        //   return User::where('id', $id)->with('activities.subject', 'activities.causer')->get();
        return  Activity::where('id', 174)->with('subject', 'causer')->get();
    }
}
