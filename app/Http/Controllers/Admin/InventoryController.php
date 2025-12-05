<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with(['product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.inventory.index', compact('variants'));
    }
}