<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SMSBundles;


class UserProxyController extends Controller
{
    public function getProxies()
    {
        $bundles = SMSBundles::all();
        return view('admin.sms-bundles', compact('bundles'));
    }
}