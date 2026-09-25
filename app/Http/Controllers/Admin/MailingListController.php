<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CustomEmail;
use App\Models\MailingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class MailingListController extends Controller
{
    public function index()
    {
        $emails = MailingList::latest()->get();

        return view('admin.mailinglist.index', compact('emails'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:mailing_list,email',
            'name' => 'nullable|string|max:255',
        ]);

        MailingList::create($request->only('email', 'name'));

        return redirect()->back()->with('success', 'E-mail toegevoegd.');
    }

    public function destroy($id)
    {
        MailingList::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'E-mail verwijderd.');
    }

    public function sendForm()
    {
        $emails = MailingList::orderBy('email')->get();

        return view('admin.mailinglist.send', compact('emails'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
        ]);

        foreach (array_chunk($request->recipients, 50) as $chunk) {
            foreach ($chunk as $recipient) {
                Mail::to($recipient)->queue(new CustomEmail(
                    $request->subject,
                    $request->message,
                    $request->type
                ));
            }
        }

        return redirect()->back()->with('success', 'E-mails zijn in de wachtrij geplaatst en worden verzonden.');
    }
}
