<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Supported locales.
     *
     * @var array
     */
    protected $supportedLocales = ['en', 'id'];

    /**
     * Switch the application locale.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string                    $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, string $locale)
    {
        if (in_array($locale, $this->supportedLocales)) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->back()->withInput();
    }
}
