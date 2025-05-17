<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 *
 */
class ClientController extends Controller
{
    /**
     * @var ClientRepository
     */
    private ClientRepository $repo;

    /**
     * @param ClientRepository $repo
     */
    public function __construct(ClientRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'phone', 'email']);
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
            'email' => 'nullable|email|max:255',
        ]);

        $client = Client::create($validated);

        return response()->json($client, Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($client, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $client->update($validated);

        return response()->json($client, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        $client->delete();

        return response()->json(['message' => 'Client deleted successfully'], Response::HTTP_OK);
    }
}
