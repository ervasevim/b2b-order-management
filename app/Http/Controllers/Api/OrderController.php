<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Resources\OrderResource;
use App\Http\Trait\HttpResponse;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use HttpResponse;

    /**
     * Create a new controller instance.
     *
     * @param OrderService $orderService
     */
    public function __construct(private readonly OrderService $orderService)
    {
    }

    /**
     * Display a list of the authenticated user's orders.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $orders = $this->orderService->getOrdersForUser($user);

        return $this->success(OrderResource::collection($orders));
    }

    /**
     * Store a newly created order in storage.
     *
     * @param StoreRequest $request
     * @param OrderService $orderService
     * @return JsonResponse
     */
    public function store(StoreRequest $request, OrderService $orderService): JsonResponse
    {
        $validated = $request->validated();

        try {
            $order = $orderService->createOrder($validated);
            return $this->success(new OrderResource($order->load('products')), 'Order has been created.')
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->error([], Response::HTTP_INTERNAL_SERVER_ERROR, 'An error occurred!', $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();

        try {
            $order = $this->orderService->findOrderById($id);

            if ($user->role !== 'admin' && $order->user_id !== $user->id) {
                return $this->error([], Response::HTTP_FORBIDDEN, 'Order not found.');
            }

            return response()->json(new OrderResource($order->load('products')));
        } catch (ModelNotFoundException) {
            return $this->error([], Response::HTTP_NOT_FOUND, 'Order not found!');
        }
    }
}
