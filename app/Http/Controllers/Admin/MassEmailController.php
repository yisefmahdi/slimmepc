<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMail;
use App\Models\MassEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MassEmailController extends Controller
{
    public function showForm()
    {
        $emails = MassEmail::latest()->paginate(10);

        return view('admin.mass-email.index', compact('emails'));
    }

    public function sendMassEmail(Request $request)
    {
        $request->validate([
            'message_type' => 'required|in:special_offer,marketing,maintenance,important_notice',
            'message_content' => 'required|string',
        ]);

        $massEmail = MassEmail::create([
            'message_type' => $request->message_type,
            'message_content' => $request->message_content,
        ]);

        $type = $massEmail->message_type;
        $content = $massEmail->message_content;

        User::whereNotNull('email')->orderBy('id')->chunk(200, function ($users) use ($type, $content) {
            foreach ($users as $user) {
                Mail::to($user->email)->queue(new BroadcastMail(
                    $user->name ?? 'Klant',
                    $type,
                    $content
                ));
            }
        });

        return back()->with('success', 'E-mails zijn in de wachtrij geplaatst en worden verzonden.');
    }

    public function deleteMassEmail($id)
    {
        MassEmail::findOrFail($id)->delete();

        return back()->with('success', 'Bericht verwijderd.');
    }
}
