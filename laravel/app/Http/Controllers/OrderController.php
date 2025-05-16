<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 *
 */
class OrderController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Order::all(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id'    => 'required|exists:clients,id',
            'driver_id'    => 'required|exists:drivers,id',
            'route_id'     => 'required|exists:routes,id',
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|in:new,done,canceled',
            'created_at'   => 'required|date',
            'completed_at' => 'nullable|date',
        ]);

        $order = Order::create($validated);

        return response()->json($order, Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($order, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'client_id'    => 'required|exists:clients,id',
            'driver_id'    => 'required|exists:drivers,id',
            'route_id'     => 'required|exists:routes,id',
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|in:new,done,canceled',
            'created_at'   => 'required|date',
            'completed_at' => 'nullable|date',
        ]);

        $order->update($validated);

        return response()->json($order, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully'], Response::HTTP_OK);
    }
}
