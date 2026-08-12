<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessSocialLinkController extends Controller
{
    private function business(Request $request, string $id): Business
    {
        return Business::ownedBy($request->attributes->get('user_id'))->findOrFail($id);
    }

    private function rules(): array
    {
        return [
            'platform' => ['required', 'string', 'max:50'],
            'username' => ['nullable', 'string', 'max:120'],
            'url' => ['required', 'url:http,https', 'max:2048'],
            'show_on_card' => ['sometimes', 'boolean'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function store(Request $request, string $business): JsonResponse
    {
        $ownedBusiness = $this->business($request, $business);
        $validated = $request->validate($this->rules());
        $validated['show_on_card'] = $validated['show_on_card'] ?? true;
        $socialLink = $ownedBusiness->socialLinks()->create($validated + [
            'user_id' => $request->attributes->get('user_id'),
        ]);

        return response()->json($socialLink, 201);
    }

    public function update(Request $request, string $business, string $social): JsonResponse
    {
        $socialLink = $this->business($request, $business)
            ->socialLinks()
            ->whereKey($social)
            ->firstOrFail();
        $rules = collect($this->rules())
            ->map(fn ($rule) => array_merge(['sometimes'], $rule))
            ->all();

        $socialLink->update($request->validate($rules));

        return response()->json($socialLink->fresh());
    }

    public function destroy(Request $request, string $business, string $social): JsonResponse
    {
        $this->business($request, $business)
            ->socialLinks()
            ->whereKey($social)
            ->firstOrFail()
            ->delete();

        return response()->json(null, 204);
    }
}
