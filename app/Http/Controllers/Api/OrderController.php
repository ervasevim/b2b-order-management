<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Resources\OrderResource;
use App\Http\Trait\HttpResponse;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly OrderService $orderService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $orders = $this->orderService->getOrdersForUser($user);

        return $this->success(OrderResource::collection($orders));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request, OrderService $orderService)
    {
        $validated = $request->validated();

        try {
            $order = $orderService->createOrder($validated);
            return $this->success(new OrderResource($order->load('products')), 'Sipariş oluşturuldu.')
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->error([], 500, 'Bir hata oluştu!', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        try {
            $order = $this->orderService->findOrderById($id);

            if ($user->role !== 'admin' && $order->user_id !== $user->id) {
                return $this->error([], 403, 'Siparişiniz bulunamadı.');
            }

            return response()->json(new OrderResource($order->load('products')));
        } catch (ModelNotFoundException) {
            return $this->error([], 404, 'Siparişiniz bulunamadı!');
        }
    }
}
