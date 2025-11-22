<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\Product;
use App\Services\BundleService;
use App\Http\Requests\Business\StoreBundleRequest;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    protected $bundleService;

    public function __construct(BundleService $bundleService)
    {
        $this->bundleService = $bundleService;
    }

    public function index(Request $request)
    {
        $query = Bundle::with('products')->withCount('products')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }


        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        if ($request->filled('stock_status')) {
            // This would need a scope or raw query since it's computed
            // For now, we'll filter after loading
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $bundles = $query->paginate(20);

        return view('bundles.index', compact('bundles'));
    }

    public function create()
    {
        $products = Product::all();
        return view('bundles.create', compact('products'));
    }

    public function store(StoreBundleRequest $request)
    {
        try {
            // Validate stock availability before creating
            $tempBundle = new Bundle($request->only(['stock_quantity']));
            $tempBundle->setRelation('products', collect($request->products)->map(function ($productData) {
                $productId = is_array($productData) ? $productData['id'] : $productData;
                $quantity = is_array($productData) ? ($productData['quantity'] ?? 1) : 1;
                
                $product = Product::find($productId);
                $product->pivot = (object)['quantity' => $quantity];
                return $product;
            }));

            $validation = $this->bundleService->canCreateWithQuantity(
                $tempBundle, 
                $request->stock_quantity ?? 0
            );

            if (!$validation['can_create']) {
                $errorMessage = "Insufficient stock for the following products:\n";
                foreach ($validation['issues'] as $issue) {
                    $errorMessage .= "- {$issue['product_name']}: needs {$issue['required']}, only {$issue['available']} available\n";
                }
                
                return back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            $bundle = $this->bundleService->create($request->validated());

            return redirect()
                ->route('bundles.show', $bundle)
                ->with('success', 'Bundle created successfully! Product stocks have been adjusted.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create bundle: ' . $e->getMessage());
        }
    }

    public function show(Bundle $bundle)
    {
        $bundle->load('products');
        
        // Increment view count
        $this->bundleService->incrementViews($bundle);
        
        // Get statistics including stock info
        $statistics = $this->bundleService->getStatistics($bundle);
        
        return view('bundles.show', compact('bundle', 'statistics'));
    }

    public function edit(Bundle $bundle)
    {
        $products = Product::active()->get();
        $bundle->load('products');
        return view('bundles.edit', compact('bundle', 'products'));
    }

    public function update(StoreBundleRequest $request, Bundle $bundle)
    {
        try {
            $bundle = $this->bundleService->update($bundle, $request->validated());

            return redirect()
                ->route('bundles.show', $bundle)
                ->with('success', 'Bundle updated successfully! Product stocks have been adjusted.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update bundle: ' . $e->getMessage());
        }
    }

    public function destroy(Bundle $bundle)
    {
        try {
            $this->bundleService->delete($bundle);

            return redirect()
                ->route('bundles.index')
                ->with('success', 'Bundle deleted successfully! Product stocks have been returned.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete bundle: ' . $e->getMessage());
        }
    }

    public function updateStock(Request $request, Bundle $bundle)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $bundle = $this->bundleService->updateStock($bundle, $request->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Bundle stock updated successfully!',
                'data' => [
                    'stock_quantity' => $bundle->stock_quantity,
                    'actual_stock_quantity' => $bundle->actual_stock_quantity,
                    'stock_status' => $bundle->stock_status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteGalleryImage(Request $request, Bundle $bundle)
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        try {
            $this->bundleService->deleteGalleryImage($bundle, $request->image_path);

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
            'bundles' => 'required|array',
            'bundles.*' => 'exists:bundles,id',
        ]);

        try {
            $count = $this->bundleService->bulkDelete($request->bundles);

            return response()->json([
                'success' => true,
                'message' => "{$count} bundles deleted successfully! Product stocks have been returned.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bundles: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'bundles' => 'required|array',
            'bundles.*' => 'exists:bundles,id',
            'status' => 'required|in:active,inactive,archived',
        ]);

        try {
            $count = $this->bundleService->bulkUpdateStatus($request->bundles, $request->status);

            return response()->json([
                'success' => true,
                'message' => "{$count} bundles updated successfully!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bundles: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function duplicate(Bundle $bundle)
    {
        try {
            $newBundle = $this->bundleService->duplicate($bundle);

            return redirect()
                ->route('bundles.edit', $newBundle)
                ->with('success', 'Bundle duplicated successfully! You can now edit the copy. Note: Stock quantity is set to 0.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to duplicate bundle: ' . $e->getMessage());
        }
    }

    /**
     * Get bundle statistics (API endpoint)
     */
    public function statistics(Bundle $bundle)
    {
        try {
            $statistics = $this->bundleService->getStatistics($bundle);

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check stock availability for bundle quantity (API endpoint)
     */
    public function checkStock(Request $request, Bundle $bundle)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $validation = $this->bundleService->canCreateWithQuantity($bundle, $request->quantity);

            return response()->json([
                'success' => true,
                'data' => $validation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check stock: ' . $e->getMessage(),
            ], 500);
        }
    }
}