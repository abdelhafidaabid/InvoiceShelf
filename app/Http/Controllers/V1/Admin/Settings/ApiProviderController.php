<?php

namespace App\Http\Controllers\V1\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiProviderRequest;
use App\Http\Resources\ApiProviderResource;
use App\Models\ApiProvider;
use App\Space\AmountToWords;
use Illuminate\Http\Request;

class ApiProviderController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $providers = ApiProvider::whereCompany()->paginate($limit);

        return ApiProviderResource::collection($providers);
    }

    public function store(ApiProviderRequest $request)
    {
        $provider = ApiProvider::createFromRequest($request);

        return new ApiProviderResource($provider);
    }

    public function show(ApiProvider $apiProvider)
    {
        return new ApiProviderResource($apiProvider);
    }

    public function update(ApiProviderRequest $request, ApiProvider $apiProvider)
    {
        $apiProvider->updateFromRequest($request);

        return new ApiProviderResource($apiProvider);
    }

    public function destroy(ApiProvider $apiProvider)
    {
        $apiProvider->delete();

        return response()->json(['success' => true]);
    }

    public function test(Request $request)
    {
        $request->validate([
            'driver' => 'required|string',
            'key' => 'required|string',
            'host' => 'required|string',
        ]);

        $result = AmountToWords::test(
            $request->key,
            $request->host,
            $request->driver
        );

        return response()->json($result);
    }
}
