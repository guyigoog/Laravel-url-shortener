<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;


class ShortUrlController extends Controller
{
    public function short(Request $request)
    {
        if($request->original_url){
            if(auth()->user())
            {
                $new_url = auth()->user()->links()->create([
                    'original_url' => $request->original_url,
                ]);
            }
            else
            {
                $new_url = ShortUrl::create([
                    'original_url' => $request->original_url,
                ]);
            }
            if($new_url){
                $short_url = base_convert((string)$new_url->id, 10, 36);
                $new_url->update([
                    'short_url' => $short_url,
                ]);
                return redirect()->back()->with('success', 'Your short url is: <a class="text-green-500" href="' . url($short_url) . '">' . url($short_url) . '</a>');
            }
        }
        return back();
    }

    public function show($code)
    {
        $short_url = ShortUrl::where('short_url', $code)->first();
        if($short_url){
            $short_url->increment('visits');
            return redirect()->to(url($short_url->original_url));
        }
        return redirect()->to(url('/'));
    }
}
