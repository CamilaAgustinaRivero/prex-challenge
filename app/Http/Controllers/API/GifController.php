<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\favs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response([
            "message" => "It works!",
        ]);
    }

    private function generateLogInfo($request, $serviceName, $responseCode = null, $responseBody = null)
    {

        $userId = auth()->id();
        $bodyRequest = $request->all();
        $ip = $request->ip();

        return "User ID: $userId | Service: $serviceName | Body: " . json_encode($bodyRequest) . " | Response Code: $responseCode | Response Body: " . json_encode($responseBody) . " | IP: $ip";
    }

    public function search(Request $request)
    {
        $apiKey = env("GIPHY_API_KEY");
        $query = $request->input('query');
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        if (!is_string($query)) {
            return response()->json(['error' => 'El parámetro query no es de tipo string'], 400);
        }

        if (!is_numeric($limit)) {
            return response()->json(['error' => 'El parámetro limit debe ser numérico'], 400);
        }

        if (!is_numeric($offset)) {
            return response()->json(['error' => 'El parámetro offset debe ser numérico'], 400);
        }

        $url = "https://api.giphy.com/v1/gifs/search?api_key=$apiKey&q=$query&limit=$limit&offset=$offset";

        $response = Http::get($url);

        if (!$response->successful()) {
            $errorMsg = 'Failed to fetch data from API';
            Log::error($this->generateLogInfo($request, 500, "GifController/Search", ['message' => $errorMsg]));
            return response()->json(['status' => 'error', 'message' => $errorMsg]);
        }

        $data = $response->json();
        if ($data === null) {
            $errorMsg = 'Failed to decode JSON response';
            Log::error($this->generateLogInfo($request, 500, "GifController/Search", ['message' => $errorMsg]));
            return response()->json(['status' => 'error', 'message' => $errorMsg]);
        }

        Log::info($this->generateLogInfo($request, 200, "GifController/Search", $data));

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function searchById(Request $request, $id)
    {
        if (!ctype_alnum($id)) {
            $errorMsg = 'El parámetro id debe ser alfanumérico';
            Log::error($this->generateLogInfo($request, 400, "GifController/SearchById", ['message' => $errorMsg]));
            return response()->json(['error' => 'El parámetro id debe ser alfanumérico'], 400);
        }

        $apiKey = env("GIPHY_API_KEY");

        $url = "https://api.giphy.com/v1/gifs/$id?api_key=$apiKey";

        $response = Http::get($url);

        if (!$response->successful()) {
            $errorMsg = 'Failed to fetch data from API';
            Log::error($this->generateLogInfo($request, 500, "GifController/SearchById", ['message' => $errorMsg]));
            return response()->json(['status' => 'error', 'message' => $errorMsg]);
        }

        $data = $response->json();
        if ($data === null) {
            $errorMsg = 'Failed to decode JSON response';
            Log::error($this->generateLogInfo($request, 500, "GifController/SearchById", ['message' => $errorMsg]));
            return response()->json(['status' => 'error', 'message' => $errorMsg]);
        }

        Log::info($this->generateLogInfo($request, 200, "GifController/SearchById", $data));

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // gif_id es alfanumerico
            'gif_id' => 'required|max:60',
            'alias' => 'required|max:80',
            'user_id' => 'required|integer'
        ]);

        favs::Create($data);
        Log::info($this->generateLogInfo($request, 200, "GifController/Store", $data));

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(favs $favs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, favs $favs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(favs $favs)
    {
        //
    }
}
