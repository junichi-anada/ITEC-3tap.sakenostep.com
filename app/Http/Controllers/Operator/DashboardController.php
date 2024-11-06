<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        return view('operator.dashboard', compact('operator'));
    }

}
