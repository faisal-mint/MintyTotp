<?php

namespace App\Http\Controllers;

use App\Models\TotpEntry;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TotpController extends Controller
{
    protected TotpService $totpService;

    public function __construct(TotpService $totpService)
    {
        $this->totpService = $totpService;
    }

    /**
     * Get all TOTP entries with current codes
     */
    public function index(): JsonResponse
    {
        $entries = TotpEntry::orderBy('name')->get()->map(function ($entry) {
            return [
                'id' => $entry->id,
                'name' => $entry->name,
                'issuer' => $entry->issuer,
                'display_name' => $entry->display_name,
                'code' => $this->totpService->generateCode($entry),
                'remaining_seconds' => $this->totpService->getRemainingSeconds($entry),
                'period' => $entry->period,
                'created_at' => $entry->created_at,
            ];
        });

        return response()->json($entries);
    }

    /**
     * Store a new TOTP entry
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'secret' => 'required|string',
            'issuer' => 'nullable|string|max:255',
            'algorithm' => 'nullable|in:sha1,sha256,sha512',
            'digits' => 'nullable|integer|in:6,8',
            'period' => 'nullable|integer|min:15|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'name',
            'secret',
            'issuer',
            'algorithm',
            'digits',
            'period',
        ]);

        // Set default values if not provided
        $data['algorithm'] = $data['algorithm'] ?? 'sha1';
        $data['digits'] = $data['digits'] ?? 6;
        $data['period'] = $data['period'] ?? 30;

        $entry = TotpEntry::create($data);

        return response()->json([
            'id' => $entry->id,
            'name' => $entry->name,
            'issuer' => $entry->issuer,
            'display_name' => $entry->display_name,
            'code' => $this->totpService->generateCode($entry),
            'remaining_seconds' => $this->totpService->getRemainingSeconds($entry),
            'period' => $entry->period,
        ], 201);
    }

    /**
     * Store a new TOTP entry from URI
     */
    public function storeFromUri(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'uri' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $this->totpService->parseUri($request->uri);
            $entry = TotpEntry::create($data);

            return response()->json([
                'id' => $entry->id,
                'name' => $entry->name,
                'issuer' => $entry->issuer,
                'display_name' => $entry->display_name,
                'code' => $this->totpService->generateCode($entry),
                'remaining_seconds' => $this->totpService->getRemainingSeconds($entry),
                'period' => $entry->period,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Get current code for an entry
     */
    public function getCode(TotpEntry $entry): JsonResponse
    {
        return response()->json([
            'id' => $entry->id,
            'code' => $this->totpService->generateCode($entry),
            'remaining_seconds' => $this->totpService->getRemainingSeconds($entry),
        ]);
    }

    /**
     * Delete a TOTP entry
     */
    public function destroy(TotpEntry $entry): JsonResponse
    {
        $entry->delete();
        return response()->json(['message' => 'Entry deleted successfully']);
    }
}
