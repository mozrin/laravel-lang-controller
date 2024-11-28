<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Translate the given key to the target language.
     *
     * @param string $key
     * @param string $iso639
     * @return \Illuminate\Http\JsonResponse
     */
    public function translate($key, $iso639)
    {
        // Set the target language
        app()->setLocale($iso639);

        // Path to the language directory (adjusted for Laravel 11)
        $langPath = base_path('lang/' . $iso639);

        // Check if the directory exists
        if (!File::exists($langPath)) {
            return response()->json([
                'message' => 'Language directory not found',
                'target_language' => $iso639,
                'lang_path' => $langPath // Debug: Output the path being checked
            ], 404);
        }

        $translation = null;
        $fileFound = null;

        if (strpos($key, '.') !== false) {
            // Key contains a dot, look in the specified file
            $translation = Lang::get($key);
            $fileFound = explode('.', $key, 2)[0];
        } else {
            // Key does not contain a dot, search all files
            $files = File::allFiles($langPath);

            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                $fileName = $file->getFilenameWithoutExtension();
                $translation = Lang::get($fileName . '.' . $key);

                if ($translation !== $fileName . '.' . $key) {
                    $fileFound = $fileName;
                    break;
                }
            }
        }

        if ($translation === null || $translation === $key || ($fileFound !== null && $translation === $fileFound . '.' . $key)) {
            return response()->json([
                'error' => "key '$key' not found",
                'target_language' => $iso639,
            ], 404);
        }

        return response()->json([
            'translation' => $translation,
            'file' => $fileFound,
            'key' => $key,
            'target_language' => $iso639,
        ]);
    }
}
