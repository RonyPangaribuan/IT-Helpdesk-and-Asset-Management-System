<?php

namespace App\Http\Requests;

use App\Enums\AssetCondition;
use App\Models\Asset;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Asset::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'asset_code' => is_string($this->input('asset_code')) ? strtoupper(trim($this->input('asset_code'))) : $this->input('asset_code'),
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'brand' => $this->nullableTrimmedString('brand'),
            'model' => $this->nullableTrimmedString('model'),
            'serial_number' => $this->nullableTrimmedString('serial_number'),
            'location' => is_string($this->input('location')) ? trim($this->input('location')) : $this->input('location'),
            'description' => $this->nullableTrimmedString('description'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'asset_code' => ['required', 'string', 'max:50', Rule::unique('assets', 'asset_code')],
            'name' => ['required', 'string', 'max:150'],
            'asset_category_id' => [
                'required',
                Rule::exists('asset_categories', 'id')->where('is_active', true)->whereNull('deleted_at'),
            ],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100', Rule::unique('assets', 'serial_number')],
            'location' => ['required', 'string', 'max:150'],
            'condition' => ['required', Rule::in(AssetCondition::values())],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function assetData(): array
    {
        $validated = $this->validated();
        $validated['is_active'] = filter_var($validated['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        if (($validated['condition'] ?? null) === AssetCondition::Retired->value) {
            $validated['is_active'] = false;
        }

        return $validated;
    }

    private function nullableTrimmedString(string $key): mixed
    {
        $value = $this->input($key);

        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
