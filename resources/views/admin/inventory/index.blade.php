@extends('admin.admin')

@section('content')
<div class="container-fluid">
    {{-- Cảnh báo sản phẩm chưa có biến thể --}}
    @if($productsWithoutVariants->count() > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Sản phẩm chưa có biến thể</h5>
        <p class="mb-0">Có <strong>{{ $productsWithoutVariants->count() }}</strong> sản phẩm chưa có size/màu:</p>
        <ul class="mt-2 mb-0">
            @foreach($productsWithoutVariants as $product)
            <li>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="alert-link">
                    {{ $product->name }}
                </a>
                - <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Thêm biến thể
                </a>
            </li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Danh sách kho hàng --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Quản lý Tồn Kho (Biến thể)</h6>
            <div>
                <span class="badge bg-secondary">Tổng: {{ $variants->total() }} biến thể</span>
            </div>
        </div>
        <div class="card-body">
            @if($variants->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Ảnh</th>
                            <th>Size</th>
                            <th>Màu</th>
                            <th>Tồn kho</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($variants as $variant)
                        <tr>
                            <td>{{ $variant->id }}</td>
                            <td>
                                <a href="{{ route('admin.products.edit', $variant->product_id) }}" class="font-weight-bold">
                                    {{ $variant->product->name ?? 'Sản phẩm đã xóa' }}
                                </a>
                                <br>
                                <small class="text-muted">SKU: {{ $variant->product->sku ?? 'N/A' }}</small>
                            </td>
                            <td class="text-center">
                                @if($variant->product && $variant->product->img_thumbnail)
                                    <img src="{{ asset($variant->product->img_thumbnail) }}" 
                                         width="50" height="50" 
                                         style="border-radius: 4px; object-fit: cover;">
                                @else
                                    <div class="bg-light" style="width: 50px; height: 50px; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ $variant->size }}</span></td>
                            <td>
                                <span class="badge" style="background-color: {{ $variant->color ?? '#6c757d' }}; color: white;">
                                    {{ $variant->color }}
                                </span>
                            </td>
                            <td>
                                @if($variant->quantity == 0)
                                    <span class="badge badge-danger">Hết hàng</span>
                                @elseif($variant->quantity < 10)
                                    <span class="badge badge-warning text-dark">Sắp hết ({{ $variant->quantity }})</span>
                                @else
                                    <span class="badge badge-success">{{ $variant->quantity }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $variant->product_id) }}" 
                                   class="btn btn-sm btn-primary" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('admin.product_variants.destroy', $variant->id) }}" 
                                      method="POST" class="d-inline" 
                                      onsubmit="return confirm('Xóa biến thể này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $variants->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Chưa có biến thể nào trong kho</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm sản phẩm mới
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection