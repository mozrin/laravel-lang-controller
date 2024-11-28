<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class LanguageController extends Controller
{
    public function translate(Request $request)
    {
        $request->validate([
            'request_key' => 'required|string',
            'language' => 'required|string',
            'browser' => 'boolean',
            'fallback' => 'boolean',
        ]);

        $request_key = $request->request_key;
        $language = $request->language;
        $browser = $request->browser ?? true;
        $fallback = $request->fallback ?? true;

        // Determine the effective language
        if ($browser && $request->hasHeader('Accept-Language')) {
            $language = substr($request->header('Accept-Language'), 0, 2);
        }

        App::setLocale($language);

        // Attempt to get the translation
        if (Lang::has($request_key)) {
            $translation = __(':key', ['key' => $request_key]);
        } else {
            if ($fallback) {
                $translation = __(':key', ['key' => $request_key], config('app.fallback_locale'));
            } else {
                return response()->json([
                    'error' => __('translation-not-found'),
                    'original_request' => $request->all()
                ], 404);
            }
        }

        return response()->json([
            'translation' => $translation,
            'original_request' => $request->all()
        ], 200);
    }
}
