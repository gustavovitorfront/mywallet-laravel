<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $periodo = $request->has('periodo') ? $request->post('periodo') : date('Y-m');
        $periodoBreaked = explode('-', $periodo);
        $year = $periodoBreaked[0];
        $month = $periodoBreaked[1];

        $transactions = Transaction::where('user_id', auth()->user()->id)->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->get();

        $totalExpenses = Transaction::where('expend', 1)
            ->where('user_id', auth()->user()->id)
            ->where('payed', 1)
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->sum('value');

        $totalRevenue = Transaction::where('expend', 0)
            ->where('user_id', auth()->user()->id)
            ->where('payed', 1)
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->sum('value');

        return view('home', [
            'transactions' => $transactions,
            'totalExpenses' => $totalExpenses,
            'totalRevenue' => $totalRevenue,
            'periodo' => $periodo
        ]);
    }
}
