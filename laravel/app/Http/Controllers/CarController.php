<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 *
 */
class CarController extends Controller
{
    /**
     * Display a listing of the cars.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cars = Car::all();
        return response()->json($cars, Response::HTTP_OK);
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
