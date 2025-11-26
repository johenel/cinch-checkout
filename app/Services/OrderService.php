<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class OrderService extends ResourceService
{
    public float $tax;

    public function __construct(Order $order = null)
    {
        $this->resource = $order;
        $this->setTaxValue();
    }

    private function setTaxValue(): void
    {
        $this->tax = 0.12;
    }

    private function prepare(string $email, string $address, string $note = null) : void
    {
        $order = new Order;
        $order->email = $email;
        $order->address = $address;
        $order->note = $note;
        $order->save();

        $this->resource = $order;
    }

    public function create(
        string $email,
        string $address,
        array|object $orders,
        string $note = null
    ) : Order {

        $this->prepare($email, $address, $note);
        $totalPrice = 0;
        $totalItemCount = 0;
        $productCount = 0;

        foreach ($orders as $order) {
            $order = (object) $order;
            $this->addItem($order);
            $totalPrice += $order->product_price * $order->quantity;
            $totalItemCount += $order->quantity;
            $productCount++;
        }

        $this->resource->total_price = $totalPrice;
        $this->resource->total_price_with_tax = $this->calculateTotalPriceWithTax($totalPrice);
        $this->resource->item_total_count = $totalItemCount;
        $this->resource->product_count = $productCount;

        return $this->resource;
    }

    public function getProducts(): Collection
    {
        $productIds = $this->resource->items()->pluck('product_id')->toArray();

        return Product::query()->whereIn('id', $productIds)->get();
    }

    private function calculateTotalPriceWithTax(float $totalPrice)
    {
        return $totalPrice + ($totalPrice * $this->tax);
    }

    private function addItem($order): void
    {
        $item = new OrderItem;
        $item->order_id = $this->resource->id;
        $item->product_id = $order->product_id;
        $item->product_name = $order->product_name;
        $item->product_description = optional($order)->product_description;
        $item->product_featured_image = optional($order)->product_featured_image;
        $item->product_price = $order->product_price;
        $item->quantity = $order->quantity;
        $item->save();
    }
}
