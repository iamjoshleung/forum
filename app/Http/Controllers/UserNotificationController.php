<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    /**
     * 
     * 
     * @return 
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * 
     * 
     * @return 
     */
    public function index(User $user) {
        return auth()->user()->unreadNotifications;
    }

    /**
     * 
     * 
     * @return 
     */
    public function destroy(User $user, $notificationId) {
        auth()->user()->notifications()->findOrFail($notificationId)->delete();
    }
}
