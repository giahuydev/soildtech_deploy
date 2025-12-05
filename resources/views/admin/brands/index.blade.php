@extends('admin.admin')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Quản lý Thương hiệu</h6>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Tên</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($brands as $brand)
                <tr>
                    <td>{{ $brand->id }}</td>
                    <td>
                        @if($brand->logo)
                            <img src="{{ asset($brand->logo) }}" width="50">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>
                    <td>{{ $brand->name }}</td>
                    <td>
                        @if($brand->is_active)
                            <span class="badge badge-success">Hiện</span>
                        @else
                            <span class="badge badge-secondary">Ẩn</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        
                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline" data-confirm-text="Xóa thương hiệu này?">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm btn-delete">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $brands->links() }}
    </div>
</div>
@endsection