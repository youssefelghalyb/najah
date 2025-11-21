<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Services\QrCodeService;
use App\Http\Requests\StoreQrCodeRequest;
use App\Http\Requests\BulkGenerateQrCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index(Request $request)
    {
        $query = QrCode::with('creator')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('uuid', 'like', "%{$search}%");
            });
        }

        $qrCodes = $query->paginate(20);

        return view('qr-codes.index', compact('qrCodes'));
    }

    public function create()
    {
        return view('qr-codes.create');
    }

    public function store(StoreQrCodeRequest $request)
    {
        try {
            $qrCode = $this->qrCodeService->generate($request->validated());

            return redirect()
                ->route('qr-codes.show', $qrCode)
                ->with('success', 'QR code generated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to generate QR code: ' . $e->getMessage());
        }
    }

    public function show(QrCode $qrCode)
    {
        $qrCode->load('creator');
        return view('qr-codes.show', compact('qrCode'));
    }

    public function edit(QrCode $qrCode)
    {
        return view('qr-codes.edit', compact('qrCode'));
    }

    public function update(StoreQrCodeRequest $request, QrCode $qrCode)
    {
        try {
            $data = $request->validated();

            $qrCode->update([
                'expires_at' => $data['expires_at'] ?? $qrCode->expires_at,
            ]);

            $designChanged = false;
            $designFields = ['foreground_color', 'background_color', 'style', 'size', 'error_correction'];

            foreach ($designFields as $field) {
                if (isset($data[$field]) && $data[$field] != $qrCode->$field) {
                    $designChanged = true;
                    break;
                }
            }

            if ($designChanged || $request->hasFile('logo')) {
                $this->qrCodeService->regenerate($qrCode, $data);
            }

            return redirect()
                ->route('qr-codes.show', $qrCode)
                ->with('success', 'QR code updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update QR code: ' . $e->getMessage());
        }
    }

    public function destroy(QrCode $qrCode)
    {
        try {
            $this->qrCodeService->delete($qrCode);

            return redirect()
                ->route('qr-codes.index')
                ->with('success', 'QR code deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete QR code: ' . $e->getMessage());
        }
    }

    public function bulkCreate()
    {
        return view('qr-codes.bulk-create');
    }

    public function bulkStore(BulkGenerateQrCodeRequest $request)
    {
        try {
            $qrCodes = $this->qrCodeService->generateBulk($request->validated());

            return redirect()
                ->route('qr-codes.bulk-preview', ['ids' => collect($qrCodes)->pluck('id')->implode(',')])
                ->with('success', count($qrCodes) . ' QR codes generated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to generate QR codes: ' . $e->getMessage());
        }
    }

    public function bulkPreview(Request $request)
    {
        $ids = explode(',', $request->ids);
        $qrCodes = QrCode::whereIn('id', $ids)->get();

        return view('qr-codes.bulk-preview', compact('qrCodes'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'qr_codes' => 'required|array',
            'qr_codes.*' => 'exists:qr_codes,id',
        ]);

        try {
            $zipPath = $this->qrCodeService->exportAsZip($request->qr_codes);

            return response()->json([
                'success' => true,
                'download_url' => Storage::disk('public')->url($zipPath),
                'message' => 'QR codes exported successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export QR codes: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'qr_codes' => 'required|array',
            'qr_codes.*' => 'exists:qr_codes,id',
        ]);

        try {
            $count = $this->qrCodeService->bulkDelete($request->qr_codes);

            return response()->json([
                'success' => true,
                'message' => "{$count} QR codes deleted successfully!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete QR codes: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(QrCode $qrCode)
    {
        if (!$qrCode->qr_image_path || !Storage::disk('public')->exists($qrCode->qr_image_path)) {
            abort(404, 'QR code image not found');
        }

        $filename = "QR-{$qrCode->code}-{$qrCode->uuid}.png";

        return Storage::disk('public')->download($qrCode->qr_image_path, $filename);
    }

    public function redirect($uuid)
    {
        $qrCode = QrCode::where('uuid', $uuid)->firstOrFail();
        $qrCode->incrementScanCount();

        if (!$qrCode->isActive()) {
            return view('qr-codes.expired', compact('qrCode'));
        }

        return view('qr-codes.redirect', compact('qrCode'));
    }

    public function toggleStatus(QrCode $qrCode)
    {
        $newStatus = $qrCode->status === 'active' ? 'inactive' : 'active';
        $qrCode->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "QR code {$newStatus}!",
        ]);
    }
}
