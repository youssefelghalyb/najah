<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\Business\StoreProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $query = Product::query()->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('stock_status')) {
            $query->where('stock_status', $request->stock_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->create($request->validated());

            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load('bundles');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        try {
            $product = $this->productService->update($product, $request->validated());

            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->productService->delete($product);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'operation' => 'required|in:set,add,subtract',
        ]);

        try {
            $this->productService->updateStock(
                $product,
                $request->quantity,
                $request->operation
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully!',
                'stock_quantity' => $product->fresh()->stock_quantity,
                'stock_status' => $product->fresh()->stock_status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteGalleryImage(Request $request, Product $product)
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        try {
            $this->productService->deleteGalleryImage($product, $request->image_path);

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
        ]);

        try {
            $count = $this->productService->bulkDelete($request->products);

            return response()->json([
                'success' => true,
                'message' => "{$count} products deleted successfully!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete products: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'status' => 'required|in:active,inactive,archived',
        ]);

        try {
            $count = $this->productService->bulkUpdateStatus($request->products, $request->status);

            return response()->json([
                'success' => true,
                'message' => "{$count} products updated successfully!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update products: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function duplicate(Product $product)
    {
        try {
            $newProduct = $this->productService->duplicate($product);

            return redirect()
                ->route('products.edit', $newProduct)
                ->with('success', 'Product duplicated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to duplicate product: ' . $e->getMessage());
        }
    }
}