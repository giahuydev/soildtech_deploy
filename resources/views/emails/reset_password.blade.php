@extends('layouts.app')

@section('body')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-5">
                    <!-- Icon -->
                    <div class="text-center mb-4">
                        <div class="forgot-icon-wrapper">
                            <i class="bi bi-key-fill"></i>
                        </div>
                    </div>
                    
                    <h3 class="text-center mb-3 fw-bold">🔐 Quên mật khẩu?</h3>
                    
                    <p class="text-muted text-center mb-4">
                        Đừng lo! Nhập email đã đăng ký và chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.
                    </p>

                    <!-- THÔNG BÁO -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {!! session('success') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- FORM -->
                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-envelope me-2"></i>Email đã đăng ký
                            </label>
                            <input type="email" name="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   placeholder="example@email.com"
                                   required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send-fill me-2"></i>
                                Gửi link đặt lại mật khẩu
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- QUAY LẠI ĐĂNG NHẬP -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Quay lại đăng nhập
                        </a>
                    </div>

                    <!-- LIÊN HỆ HỖ TRỢ -->
                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="text-muted small mb-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Bạn cần hỗ trợ?
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-telephone-fill text-primary me-2"></i>
                            Hotline: <strong class="text-primary">1900.633.349</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 15px !important;
        overflow: hidden;
    }
    
    .forgot-icon-wrapper {
        display: inline-flex;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        animation: pulse 2s infinite;
    }
    
    .forgot-icon-wrapper i {
        font-size: 2.5rem;
        color: white;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(245, 87, 108, 0.7);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 15px rgba(245, 87, 108, 0);
        }
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #0d6efd;
    }
    
    .btn {
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .alert {
        border-radius: 10px;
        border: none;
    }
</style>
@endsection