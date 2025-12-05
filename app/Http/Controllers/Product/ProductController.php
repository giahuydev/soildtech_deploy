<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * 1. Trang danh sách tất cả sản phẩm
     */
    public function index()
    {
        $products = Product::where('is_active', true)->paginate(9);
        $categoryName = "Tất cả sản phẩm";
        return view('products.index', compact('products', 'categoryName'));
    }

    /**
     * 2. Lọc sản phẩm theo danh mục
     */
    public function getByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)
                           ->where('is_active', true)
                           ->paginate(9);

        return view('products.index', [
            'products' => $products,
            'categoryName' => $category->name
        ]);
    }

    /**
     * 3. CHI TIẾT SẢN PHẨM
     */
    public function detail($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'brand', 'variants'])
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('products.detail', compact('product', 'relatedProducts'));
    }

    /**
     * 4. Trang Hot Sale
     */
    public function hotSale()
    {
        $products = Product::where('is_active', true)
                           ->whereNotNull('price_sale') // Chỉ lấy cái nào có giá sale
                           ->whereColumn('price_sale', '<', 'price') // Đảm bảo giá sale nhỏ hơn giá gốc
                           ->latest() // Mới nhất lên đầu
                           ->paginate(9);
        
        return view('products.index', [
            'products' => $products,
            'categoryName' => 'Săn Sale Giá Sốc 🔥'
        ]);
    }

    private function getImageUrl($imagePath)
{
    if (empty($imagePath)) {
        return 'https://placehold.co/400x400/f8f9fa/999?text=No+Image';
    }
    
    // Nếu đã có http/https thì return luôn
    if (str_starts_with($imagePath, 'http')) {
        return $imagePath;
    }
    
    // Nếu có /storage thì return luôn
    if (str_starts_with($imagePath, '/storage')) {
        return asset($imagePath);
    }
    
    // Ngược lại thêm /storage
    return asset('storage/' . $imagePath);
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}