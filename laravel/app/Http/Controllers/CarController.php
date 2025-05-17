<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 *
 */
class CarController extends Controller
{
    /**
     * @var CarRepository
     */
    private CarRepository $repo;

    /**
     * @param CarRepository $repo
     */
    public function __construct(CarRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['model', 'license_plate', 'driver_id']);
        $perPage = (int) $request->query('itemsPerPage', 10);
        $page    = (int) $request->query('page', 1);

        $data = $this->repo->getAllByFilter($filters, $perPage, $page);

        return response()->json($data, JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created car.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model'         => 'required|string|max:100',
            'license_plate' => 'required|string|max:20',
            'driver_id'     => 'required|exists:drivers,id',
        ]);

        $car = Car::create($validated);

        return response()->json($car, Response::HTTP_CREATED);
    }

    /**
     * Display the specified car.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($car, Response::HTTP_OK);
    }

    /**
     * Update the specified car.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'model'         => 'required|string|max:100',
            'license_plate' => 'required|string|max:20',
            'driver_id'     => 'required|exists:drivers,id',
        ]);

        $car->update($validated);

        return response()->json($car, Response::HTTP_OK);
    }

    /**
     * Remove the specified car.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);
        }

        $car->delete();

        return response()->json(['message' => 'Car deleted successfully'], Response::HTTP_OK);
    }
}
