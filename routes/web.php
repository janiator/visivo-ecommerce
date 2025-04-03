<?php

use Illuminate\Http\File;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-supabase', function () {
    $filePath = storage_path('app/example2.png');

    if (!file_exists($filePath)) {
        return 'Source file does not exist at: ' . $filePath;
    }

    $contents = file_get_contents($filePath);
    $destination = 'test/example2.png';

    try {
        $result = Storage::disk('s3')->put($destination, $contents, 'public');

        Log::info('Supabase upload using put() result:', ['result' => $result]);

        return $result
            ? 'File stored at: ' . $destination
            : 'No file stored. Result returned false.';
    } catch (Exception $e) {
        Log::error('Error during Supabase upload (put):', ['error' => $e->getMessage()]);
        return 'An error occurred: ' . $e->getMessage();
    }
});

Route::get('/list-buckets', function () {
    // Construct the URL using your Supabase endpoint (defined in config/filesystems.php)
    $endpoint = config('filesystems.disks.supabase.endpoint');

    // Use the service key. Make sure it's defined in your .env file.
    $apiKey = env('SUPABASE_SERVICE_KEY');

    if (!$apiKey) {
        Log::error('Supabase service key is missing from configuration.');
        return response()->json([
            'error' => __('messages.missing_supabase_service_key')
        ], 500);
    }

    $client = new Client();

    try {
        $response = $client->request('GET', $endpoint, [
            'headers' => [
                'apikey'       => $apiKey,
                'Authorization'=> 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return response()->json($data);
    } catch (\Exception $e) {
        Log::error('Error listing Supabase buckets: ' . $e->getMessage());

        return response()->json([
            'error' => __('messages.error_listing_buckets', ['error' => $e->getMessage()])
        ], 500);
    }
});
