<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Hiển thị danh sách kho hàng
     */
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product.brand', 'product.category']);

        // Tìm kiếm theo tên sản phẩm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái tồn kho
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('quantity', [1, 10]);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
            }
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'updated_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $variants = $query->paginate(15);
        
        // Lấy danh sách sản phẩm chưa có biến thể
        $productsWithoutVariants = Product::whereDoesntHave('variants')
            ->where('is_active', 1)
            ->get();

        return view('admin.inventory.index', compact('variants', 'productsWithoutVariants'));
    }

    /**
     * Cập nhật số lượng tồn kho
     */
    public function updateQuantity(Request $request, ProductVariant $variant)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $variant->update([
            'quantity' => $request->quantity,
        ]);

        return redirect()->back()->with('success', 'Cập nhật số lượng thành công!');
    }

    /**
     * ✅ XÓA BIẾN THỂ - NẾU LÀ VARIANT CUỐI → XÓA LUÔN SẢN PHẨM
     */
    public function destroy(ProductVariant $variant)
{
    try {
        DB::beginTransaction();

        $product = $variant->product;
        $productName = $product->name;
        $productId = $product->id;

        Log::info("=== BẮT ĐẦU XÓA VARIANT ===");
        Log::info("Variant ID: {$variant->id}");
        Log::info("Sản phẩm: {$productName} (ID: {$productId})");

        // ✅ ĐẾM SỐ VARIANTS HIỆN TẠI
        $currentVariantsCount = DB::table('product_variants')
            ->where('product_id', $productId)
            ->count();
        
        Log::info("Số variants hiện tại: {$currentVariantsCount}");

        // Xóa variant này
        $variant->delete();
        Log::info("Đã xóa variant ID: {$variant->id}");

        // ✅ ĐẾM LẠI SAU KHI XÓA
        $remainingVariants = DB::table('product_variants')
            ->where('product_id', $productId)
            ->count();
        
        Log::info("Còn lại: {$remainingVariants} variants");

        // ✅ NẾU KHÔNG CÒN VARIANT NÀO → XÓA SẢN PHẨM
        if ($remainingVariants === 0) {
            Log::info("KHÔNG CÒN VARIANT NÀO → XÓA SẢN PHẨM {$productName}");
            
            // Xóa ảnh
            if ($product->img_thumbnail) {
                $imagePath = public_path($product->img_thumbnail);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                    Log::info("Đã xóa ảnh: {$product->img_thumbnail}");
                }
            }

            // Xóa sản phẩm
            $product->delete();
            Log::info("ĐÃ XÓA SẢN PHẨM {$productName}");

            DB::commit();

            return redirect()->route('admin.inventory')
                ->with('warning', "⚠️ Đã xóa variant cuối cùng. Sản phẩm \"{$productName}\" đã bị XÓA khỏi hệ thống!");
        }

        Log::info("Sản phẩm {$productName} còn {$remainingVariants} variants → KHÔNG xóa");

        DB::commit();

        return redirect()->route('admin.inventory')
            ->with('success', "Đã xóa variant! Sản phẩm \"{$productName}\" còn {$remainingVariants} variant.");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ LỖI KHI XÓA: " . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        return redirect()->route('admin.inventory')
            ->with('error', 'Lỗi: ' . $e->getMessage());
    }
}

    /**
     * ✅ XÓA NHIỀU VARIANTS CÙNG LÚC
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'exists:product_variants,id',
        ]);

        try {
            DB::beginTransaction();

            $variants = ProductVariant::whereIn('id', $request->variant_ids)->get();
            $productIds = $variants->pluck('product_id')->unique();

            Log::info("Bulk delete: Xóa " . count($request->variant_ids) . " variants");

            // Xóa các biến thể đã chọn
            ProductVariant::whereIn('id', $request->variant_ids)->delete();

            // Kiểm tra và xóa sản phẩm không còn biến thể
            $deletedProducts = [];
            $remainingProducts = [];

            foreach ($productIds as $productId) {
                $product = Product::find($productId);
                
                if ($product) {
                    $remainingVariants = ProductVariant::where('product_id', $productId)->count();
                    
                    Log::info("Sản phẩm {$product->name} còn {$remainingVariants} variants");
                    
                    // ✅ NẾU KHÔNG CÒN VARIANT NÀO → XÓA SẢN PHẨM
                    if ($remainingVariants === 0) {
                        // Xóa ảnh
                        if ($product->img_thumbnail && File::exists(public_path($product->img_thumbnail))) {
                            File::delete(public_path($product->img_thumbnail));
                        }
                        
                        $deletedProducts[] = $product->name;
                        $product->delete();
                        
                        Log::info("Đã xóa sản phẩm {$product->name}");
                    } else {
                        $remainingProducts[] = [
                            'name' => $product->name,
                            'variants' => $remainingVariants
                        ];
                    }
                }
            }

            DB::commit();

            // Tạo message chi tiết
            $message = '✅ Đã xóa ' . count($request->variant_ids) . ' biến thể.';
            
            if (count($deletedProducts) > 0) {
                $message .= ' ⚠️ Đã xóa ' . count($deletedProducts) . ' sản phẩm: ' . implode(', ', $deletedProducts);
            }
            
            if (count($remainingProducts) > 0) {
                $message .= ' ℹ️ Sản phẩm còn lại: ';
                foreach ($remainingProducts as $rp) {
                    $message .= $rp['name'] . ' (' . $rp['variants'] . ' variants), ';
                }
            }

            return redirect()->route('admin.inventory')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi bulk delete: " . $e->getMessage());
            
            return redirect()->route('admin.inventory')
                ->with('error', 'Lỗi khi xóa hàng loạt: ' . $e->getMessage());
        }
    }

    public function deleteAllVariantsOfProduct($productId)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($productId);
            $productName = $product->name;
            $variantsCount = ProductVariant::where('product_id', $productId)->count();

            Log::info("Xóa tất cả {$variantsCount} variants của sản phẩm {$productName}");

            // Xóa tất cả variants
            ProductVariant::where('product_id', $productId)->delete();

            // Xóa ảnh sản phẩm
            if ($product->img_thumbnail && File::exists(public_path($product->img_thumbnail))) {
                File::delete(public_path($product->img_thumbnail));
            }

            // Xóa sản phẩm
            $product->delete();

            DB::commit();

            return redirect()->route('admin.inventory')
                ->with('success', "✅ Đã xóa {$variantsCount} variants và sản phẩm \"{$productName}\"!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi xóa tất cả variants: " . $e->getMessage());
            
            return redirect()->route('admin.inventory')
                ->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
}