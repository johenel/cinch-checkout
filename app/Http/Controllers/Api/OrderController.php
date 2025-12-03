<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected ProductService $productService
    )
    {
        //
    }

    public function store(StoreRequest $request)
    {
        $email = $request->get('email');
        $address = $request->get('address');
        $note = $request->get('note');
        $orders = (object) $request->get('orders');

        $order = $this->orderService->create($email, $address, $orders, $note);

        // Once order is created, reduce stock of products included in the order
        $items = $order->items;
        foreach ($items as $item) {
            $product = Product::find($item->product_id);
            if (!empty($product)) {
                $this->productService->use($product)->reduceStock($item->quantity);
            } else {
                Log::error("Trying to reduce stock of an invalid product from order #{$order->id} -> product #{$item->product_id}");
            }
        }

        return response()->json($order);
    }
}
