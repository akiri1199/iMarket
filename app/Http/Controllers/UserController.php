<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Exports\UsersExport;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;


class UserController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::all(),
        ]);
    }

    public function import(Request $request)
    {
        // use Excel facade to import data, by passing in the UserImport class and the uploaded file request as arguments
        Excel::import(new UserImport, $request->file('file')->store('temp'));

        return redirect('/')->with('success', 'All good!');
    }

    public function export()
    {
        // use Excel facade to export data, by passing in the UserExport class and the desired file name as arguments
        return Excel::download(new UsersExport, 'users.xlsx');
    }

}
