<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 *
 */
class RouteController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Route::all(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_point' => 'required|string',
            'end_point'   => 'required|string',
            'distance_km' => 'required|numeric|min:0',
        ]);

        $route = Route::create($validated);

        return response()->json($route, Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Route not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($route, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Route not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'start_point' => 'required|string',
            'end_point'   => 'required|string',
            'distance_km' => 'required|numeric|min:0',
        ]);

        $route->update($validated);

        return response()->json($route, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Route not found'], Response::HTTP_NOT_FOUND);
        }

        $route->delete();

        return response()->json(['message' => 'Route deleted successfully'], Response::HTTP_OK);
    }
}
