<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\WebSettings;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    public function generateAPIToken()
    {
        WebSettings::find(1)->update([
            'api_access_token' => Str::random(150),
        ]);

        return back()->with('success', 'API Token Generated Successfully');
    }
}
