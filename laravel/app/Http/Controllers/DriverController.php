<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Repositories\DriverRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 *
 */
class DriverController extends Controller
{
    /**
     * @var DriverRepository
     */
    private DriverRepository $repo;

    /**
     * @param DriverRepository $repo
     */
    public function __construct(DriverRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'phone', 'status']);
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
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'status'=> 'required|in:active,inactive',
        ]);

        $driver = Driver::create($validated);

        return response()->json($driver, Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($driver, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'status'=> 'required|in:active,inactive',
        ]);

        $driver->update($validated);

        return response()->json($driver, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], Response::HTTP_NOT_FOUND);
        }

        $driver->delete();

        return response()->json(['message' => 'Driver deleted successfully'], Response::HTTP_OK);
    }
}
