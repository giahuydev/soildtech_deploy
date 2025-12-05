@extends('admin.admin')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Quản lý Sản phẩm</h6>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>SKU</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if($product->img_thumbnail)
                                <img src="{{ asset($product->img_thumbnail) }}" width="50" class="img-thumbnail">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <br>
                            <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                        </td>
                        <td>{{ $product->sku ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $product->category->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $product->brand->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <strong class="text-danger">{{ number_format($product->price) }}đ</strong>
                            @if($product->price_sale)
                                <br>
                                <small class="text-success">Sale: {{ number_format($product->price_sale) }}đ</small>
                            @endif
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="badge badge-success">Hiện</span>
                            @else
                                <span class="badge badge-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm mb-1">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" data-confirm-text="Xóa sản phẩm này? Tất cả biến thể cũng sẽ bị xóa!">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm mb-1 btn-delete">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection