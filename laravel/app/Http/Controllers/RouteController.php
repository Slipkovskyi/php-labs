<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Repositories\RouteRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 *
 */
class RouteController extends Controller
{
    /**
     * @var RouteRepository
     */
    private RouteRepository $repo;

    /**
     * @param RouteRepository $repo
     */
    public function __construct(RouteRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['start_point', 'end_point']);
        $perPage = (int) $request->query('itemsPerPage', 10);
        $page    = (int) $request->query('page', 1);

        $data = $this->repo->getAllByFilter($filters, $perPage, $page);

        return response()->json($data, JsonResponse::HTTP_OK);
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
