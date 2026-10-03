<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'addresses' => $request->user()->addresses()
                ->orderByDesc('is_default')
                ->latest()
                ->get()
                ->map(fn ($a) => $this->payload($a)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'label' => ['required', 'in:home,office,other'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'area' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'landmark' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['boolean'],
        ]);

        $user = $request->user();

        if ($data['is_default'] ?? false) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create($data);

        return response()->json(['address' => $this->payload($address)], 201);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'label' => ['required', 'in:home,office,other'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'area' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'landmark' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['boolean'],
        ]);

        if ($data['is_default'] ?? false) {
            $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json(['address' => $this->payload($address->fresh())]);
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $address->delete();

        return response()->json(['message' => 'تم حذف العنوان']);
    }

    private function payload(Address $a): array
    {
        return [
            'id' => $a->id,
            'label' => $a->label,
            'governorate_id' => $a->governorate_id,
            'governorate' => $a->governorate?->name,
            'shipping_fee' => $a->shippingFee(),
            'delivery_days' => $a->governorate?->delivery_days,
            'area' => $a->area,
            'street' => $a->street,
            'building' => $a->building,
            'floor' => $a->floor,
            'apartment' => $a->apartment,
            'landmark' => $a->landmark,
            'phone' => $a->phone,
            'lat' => $a->lat !== null ? (float) $a->lat : null,
            'lng' => $a->lng !== null ? (float) $a->lng : null,
            'is_default' => $a->is_default,
        ];
    }
}
