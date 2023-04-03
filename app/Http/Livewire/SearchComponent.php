<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;
use Livewire\WithPagination;

class SearchComponent extends Component
{
    use WithPagination;
    public $pageSize = 12;
    public $orderBy = "Default Sorting";

    public $q;
    public $search_term;

    public function mount()
    {
        $this->fill(request()->only('q'));
        $this->search_term = '%' . $this->q . '%';
    }
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
            $products = Product::where('name','like',$this->search_term)->orderBy('sale_price', 'ASC')->paginate($this->pageSize);
        } elseif ($this->orderBy == 'Price: High to Low') {
            $products = Product::where('name','like',$this->search_term)->orderBy('sale_price', 'DESC')->paginate($this->pageSize);
        } elseif ($this->orderBy == 'Sort By Newness') {
            $products = Product::where('name','like',$this->search_term)->orderBy('created_at', 'DESC')->paginate($this->pageSize);
        } else {
            $products = Product::where('name','like',$this->search_term)->paginate($this->pageSize);
        }
        $categories = Category::orderBy('name', 'ASC');

        return view('livewire.search-component', compact('products', 'categories'));
    }
}
