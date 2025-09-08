<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Services\NoticeMailService;
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

        return redirect()->route('admin-index', ['tab' => 'store'])
            ->with('message', '店舗代表者を作成しました');
    }

    public function send(Request $request, NoticeMailService $service)
    {
        $subject = $request->input('subject');
        $body = $request->input('body');

        $recipients = $service->getRecipientsForAdmin();

        $service->send($recipients, $subject, $body);

        return redirect()->route('admin-index', ['tab' => 'send'])
            ->with('message', 'メールを送信しました');
    }
}
