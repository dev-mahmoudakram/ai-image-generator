<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (in_array($locale, ['ar', 'en'])) {
            session(['locale' => $locale]);
        }

        return redirect()->back();
    }
}
