<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin-index');
    }

    public function store(Request $request)
    {
        $owner = $request->only([
            'name',
            'email',
        ]);

        $owner['password'] = Hash::make($owner['password']);

        Owner::create($owner);

        return redirect()->route('admin-index')
            ->with('message', '店舗代表者を登録しました');
    }
}
