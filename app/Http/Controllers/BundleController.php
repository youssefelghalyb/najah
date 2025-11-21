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
        $products = Product::active()->get();
        return view('bundles.create', compact('products'));
    }

    public function store(StoreBundleRequest $request)
    {
        try {
            $bundle = $this->bundleService->create($request->validated());

            return redirect()
                ->route('bundles.show', $bundle)
                ->with('success', 'Bundle created successfully!');
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
        
        return view('bundles.show', compact('bundle'));
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
                ->with('success', 'Bundle updated successfully!');
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
                ->with('success', 'Bundle deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete bundle: ' . $e->getMessage());
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
                'message' => "{$count} bundles deleted successfully!",
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
                ->with('success', 'Bundle duplicated successfully! You can now edit the copy.');
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
}