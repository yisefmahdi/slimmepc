<?php

namespace App\Http\Controllers;

use App\Models\DeviceReceipt;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(Request $request): View
    {
        $tNumber = $request->query('t_number');
        $c = Cms::page('home');
        $design = Cms::design();

        return view('tracking.index', compact('tNumber', 'c', 'design'));
    }

    public function track(Request $request)
    {
        $request->validate([
            't_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $tNumber = $request->t_number;
        $email = $request->email;

        $receipt = DeviceReceipt::where('customer_email', $email)
            ->where(function($q) use ($tNumber) {
                $q->where('id', 'like', "%$tNumber%") 
                  ->orWhereRaw("CONCAT('DR-', LPAD(id, 5, '0')) = ?", [$tNumber]);
            })
            ->first();

        if (!$receipt) {
            $c = Cms::page('home');
            $design = Cms::design();
            return back()->withInput()->with('c', $c)->with('design', $design)->withErrors(['msg' => 'Geen order gevonden met dit nummer en e-mailadres.']);
        }

        $c = Cms::page('home');
        $design = Cms::design();

        return view('tracking.status', compact('receipt', 'c', 'design'));
    }
}
