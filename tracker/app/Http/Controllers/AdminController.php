<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Tracker;

class AdminController extends Controller
{
    public function __construct()
		{
			$this->middleware('auth');
		}

    //
    public function index()
	{	$data = Tracker::getAllStatistic();
		return view('admin', compact("data"));
	}
}
