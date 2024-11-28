The LanguageController.php file is a Laravel controller designed to handle language translation requests. Its primary function is to translate given keys into specified target languages by looking up corresponding translation files.

Functionality
Translate Method
Endpoint: /api/translate/{key}/{target_language}

The translate method in LanguageController performs the following tasks:

Set Locale:

The method starts by setting the application locale to the target language specified in the URL parameter ({target_language}).

Check Language Directory:

It checks if the translation directory for the specified language exists in the project root's lang directory. If the directory does not exist, it returns a JSON response with an error message indicating that the language directory was not found.

Key Handling:

If the translation key ({key}) contains a dot (.), the method assumes it specifies both the file and the key (e.g., messages.welcome), and it only searches in the specified file.

If the key does not contain a dot, the method searches across all translation files in the specified language directory.

Retrieve Translation:

The method retrieves the translation for the specified key. If found, it records the file in which the translation was found.

If the key is not found in any file, it returns a JSON response with an error message indicating that the key was not found.

Response Format:

If the translation is found, it returns a JSON response with the translation, the file name, the key, and the target language.

If the key is not found, it returns an error message in the JSON response.

The following route (or similar) is required:

  use App\Http\Controllers\LanguageController;

  Route::get('/translate/{key}/{target_language}', [LanguageController::class, 'translate']);
