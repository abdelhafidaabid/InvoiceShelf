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
        $provider = ApiProvider::create(array_merge($request->validated(), [
            'config' => $request->config,
            'company_id' => $request->header('company'),
        ]));

        return new ApiProviderResource($provider);
    }

    public function show(ApiProvider $apiProvider)
    {
        return new ApiProviderResource($apiProvider);
    }

    public function update(ApiProviderRequest $request, ApiProvider $apiProvider)
    {
        $apiProvider->update(array_merge($request->validated(), [
            'config' => $request->config,
        ]));

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
            'key' => 'nullable|string',
            'host' => 'required|string',
            'config.keys' => 'nullable|array',
        ]);

        $keys = array_filter(array_unique(array_merge(
            [$request->key],
            $request->input('config.keys', [])
        )));

        $result = AmountToWords::test(
            $keys,
            $request->host,
            $request->driver
        );

        return response()->json($result);
    }
}
