<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Notifivation;

class NotificationController extends Controller
{
    public function index(){
        $notifications = Notification::all();

        return view('notifications.index', compact('notifications'));
    }
}
