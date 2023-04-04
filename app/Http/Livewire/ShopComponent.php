<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;
use Livewire\WithPagination;

class ShopComponent extends Component
{
    use WithPagination;
    public $pageSize = 12;
    public $orderBy = "Default Sorting";
    public $min_value = 0;
    public $max_value = 1000;

    public function store($product_id, $product_name, $product_price)
    {
        Cart::add($product_id, $product_name, 1, $product_price)->associate('App\Models\Product');
        session()->flash('success_message', 'item added in cart');
        return redirect()->route('shop.cart');
    }
    public function changePageSize($size)
    {
        $this->pageSize = $size;
    }
    public function changOrderBy($order)
    {
        $this->orderBy = $order;
    }

    public function render()
    {
        if ($this->orderBy == 'Price: Low to High') {
            $products = Product::whereBetween('sale_price', [$this->min_value, $this->max_value])
                ->orderBy('sale_price', 'ASC')->paginate($this->pageSize);
        } elseif ($this->orderBy == 'Price: High to Low') {
            $products = Product::whereBetween('sale_price', [$this->min_value, $this->max_value])
                ->orderBy('sale_price', 'DESC')->paginate($this->pageSize);
        } elseif ($this->orderBy == 'Sort By Newness') {
            $products = Product::whereBetween('sale_price', [$this->min_value, $this->max_value])
                ->orderBy('created_at', 'DESC')->paginate($this->pageSize);
        } else {
            $products = Product::whereBetween('sale_price', [$this->min_value, $this->max_value])
                ->paginate($this->pageSize);
        }
        $categories = Category::orderBy('name', 'ASC');
        return view('livewire.shop-component', compact('products', 'categories'));
    }
}
