<?php

namespace App\Http\Controllers;

use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionSelfieController extends Controller
{
    public function __invoke(Request $request, string $token, SubmissionService $submissions): JsonResponse
    {
        $validated = $request->validate([
            'selfie' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $submission = $submissions->findByToken($token);
        $asset = $submissions->attachSelfie($submission, $validated['selfie']);

        return response()->json([
            'uploaded' => true,
            'asset_id' => $asset->id,
        ]);
    }
}
