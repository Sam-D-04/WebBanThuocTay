<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->latest()->paginate(10);
        return response()->json($orders);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load('items.product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_name' => 'required|string',
            'shipping_phone' => 'required|string',
            'shipping_address' => 'required|string'
        ]);

        $total = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($product->stock < $item['quantity']) {
                return response()->json(['message' => 'Insufficient stock for ' . $product->name], 400);
            }

            $price = $product->price * (1 - $product->discount_percent / 100);
            $total += $price * $item['quantity'];

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $price
            ];

            $product->decrement('stock', $item['quantity']);
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'total' => $total,
            'status' => 'pending',
            'shipping_name' => $validated['shipping_name'],
            'shipping_phone' => $validated['shipping_phone'],
            'shipping_address' => $validated['shipping_address']
        ]);

        foreach ($orderItems as $item) {
            $item['order_id'] = $order->id;
            OrderItem::create($item);
        }

        return response()->json($order->load('items.product'), 201);
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->update($validated);
        return response()->json($order);
    }

    public function adminIndex(Request $request)
    {

        $orders = Order::with(['items.product', 'user'])
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }
}
