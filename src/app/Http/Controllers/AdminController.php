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

        return redirect()->route('admin-index')
            ->with('message', '店舗代表者を登録しました');
    }

    public function sendNotice(Request $request, NoticeMailService $service)
    {
        $subject = $request->input('subject');
        $body = $request->input('body');

        $recipients = $service->getRecipientsForAdmin();

        $service->send($recipients, $subject, $body);

        return back()->with('message', 'メールを送信しました');
    }


    // オーナーの場合　$recipients = $service->getRecipientsForShop(auth()->user()->shop_id);

}
