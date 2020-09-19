<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\StatusLiked;

class BroadcastController extends Controller
{
    public function SendMessage(Request $request){
		
        
        event(new StatusLiked($request->message));
        return "Ok";

    }
}
