<div class="product-card h-100">
    <!-- Image Container -->
    <div class="position-relative overflow-hidden rounded-top" style="height: 250px;">
        <a href="{{ route('shop.detail', $product->slug) }}">
            @php
                // Xử lý đường dẫn ảnh
                $imageUrl = $product->img_thumbnail;
                if (empty($imageUrl)) {
                    $imageUrl = 'https://placehold.co/400x400/f8f9fa/999?text=No+Image';
                } elseif (!str_starts_with($imageUrl, 'http')) {
                    // Nếu không phải URL đầy đủ, thêm /storage
                    $imageUrl = asset('storage/' . ltrim($imageUrl, '/'));
                }
            @endphp
            <img src="{{ $imageUrl }}" 
                 class="w-100 h-100 object-fit-cover product-img" 
                 alt="{{ $product->name }}"
                 loading="lazy"
                 onerror="this.src='https://placehold.co/400x400/f8f9fa/999?text=No+Image'">
        </a>
        
        <!-- Discount Badge -->
        @if($product->price_sale && $product->price_sale < $product->price)
            @php
                $discount = round((($product->price - $product->price_sale) / $product->price) * 100);
            @endphp
            <span class="badge bg-danger position-absolute top-0 start-0 m-2 rounded-pill fs-6 fw-bold">
                -{{ $discount }}%
            </span>
        @endif
        
        <!-- Action Buttons -->
        <div class="action-buttons position-absolute top-50 start-50 translate-middle w-100 text-center" 
             style="opacity: 0; transition: opacity 0.3s;">
            @auth
                @if($product->variants->isNotEmpty())
                    @php
                        $firstVariant = $product->variants->first();
                    @endphp
                    <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="size" value="{{ $firstVariant->size }}">
                        <input type="hidden" name="color" value="{{ $firstVariant->color }}">
                        <input type="hidden" name="quantity" value="1">
                        
                        <button type="submit" class="btn btn-white rounded-circle shadow me-2" 
                                title="Thêm vào giỏ">
                            <i class="bi bi-cart-plus"></i>
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-white rounded-circle shadow me-2" 
                   title="Đăng nhập để mua hàng">
                    <i class="bi bi-cart-plus"></i>
                </a>
            @endauth
            
            <a href="{{ route('shop.detail', $product->slug) }}" 
               class="btn btn-white rounded-circle shadow" 
               title="Xem chi tiết">
                <i class="bi bi-eye"></i>
            </a>
        </div>
    </div>
    
    <!-- Card Body -->
    <div class="card-body p-3">
        <!-- Brand -->
        <div class="text-muted text-uppercase small mb-1">
            {{ $product->brand->name ?? 'Thương hiệu' }}
        </div>
        
        <!-- Product Name -->
        <h6 class="card-title mb-2" style="height: 40px; overflow: hidden;">
            <a href="{{ route('shop.detail', $product->slug) }}" 
               class="text-dark text-decoration-none" 
               title="{{ $product->name }}">
                {{ Str::limit($product->name, 50) }}
            </a>
        </h6>
        
        <!-- Price -->
        <div class="price-box mb-2">
            @if($product->price_sale && $product->price_sale < $product->price)
                <span class="text-danger fw-bold fs-5 me-2">
                    {{ number_format($product->price_sale) }}đ
                </span>
                <span class="text-muted text-decoration-line-through small">
                    {{ number_format($product->price) }}đ
                </span>
            @else
                <span class="fw-bold text-dark fs-5">
                    {{ number_format($product->price) }}đ
                </span>
            @endif
        </div>
        
        <!-- Stock Status -->
        @if($product->variants->sum('quantity') > 0)
            <small class="text-success">
                <i class="bi bi-check-circle-fill"></i> Còn hàng
            </small>
        @else
            <small class="text-danger">
                <i class="bi bi-x-circle-fill"></i> Hết hàng
            </small>
        @endif
    </div>
</div>

<style>
    .product-card {
        transition: transform 0.3s, box-shadow 0.3s;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .product-card:hover .action-buttons {
        opacity: 1 !important;
    }

    .product-card .product-img {
        transition: transform 0.3s;
    }

    .product-card:hover .product-img {
        transform: scale(1.1);
    }

    .product-card .card-title a:hover {
        color: #dc3545 !important;
    }

    .action-buttons .btn {
        width: 40px;
        height: 40px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .action-buttons .btn:hover {
        background-color: #dc3545 !important;
        color: white !important;
    }
</style>