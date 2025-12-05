@extends('admin.admin')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Danh mục</h6>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Slug</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td>{{ $cat->name }}</td>
                        <td>{{ $cat->slug }}</td>
                        <td>
                            @if($cat->is_active)
                                <span class="badge badge-success">Hiện</span>
                            @else
                                <span class="badge badge-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            
                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="d-inline" data-confirm-text="Xóa danh mục này? Sản phẩm thuộc danh mục cũng có thể bị ảnh hưởng!">
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
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection