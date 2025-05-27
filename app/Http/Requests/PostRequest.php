<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for validating post data.
 */
class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'status' => ['required', Rule::in(['not_found', 'found'])],
            'breed' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'numeric'],
            'email' => ['required', 'email', 'max:255'],
            'photo_urls' => ['required', 'array'],
            'photo_urls.*' => ['required', 'string', 'url', 'max:2048']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your post.',
            'title.max' => 'The title cannot exceed 255 characters.',
            'description.required' => 'Please provide a description.',
            'description.min' => 'The description must be at least 10 characters long.',
            'status.required' => 'Please specify if the pet is found or not found.',
            'status.in' => 'The status must be either "found" or "not found".',
            'breed.required' => 'Please specify the breed.',
            'breed.max' => 'The breed name cannot exceed 255 characters.',
            'location.required' => 'Please provide the location.',
            'location.max' => 'The location cannot exceed 255 characters.',
            'mobile_number.required' => 'Please provide a contact number.',
            'mobile_number.numeric' => 'The contact number must be numeric.',
            'email.required' => 'Please provide an email address.',
            'email.email' => 'Please provide a valid email address.',
            'photo_urls.required' => 'Please upload at least one photo.',
            'photo_urls.array' => 'The photos must be provided as an array.',
            'photo_urls.*.required' => 'Each photo URL is required.',
            'photo_urls.*.url' => 'Each photo must be a valid URL.',
            'photo_urls.*.max' => 'Each photo URL cannot exceed 2048 characters.'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('photo_urls')) {
            $this->merge([
                'photo_urls' => is_string($this->photo_urls)
                    ? json_decode($this->photo_urls, true)
                    : $this->photo_urls
            ]);
        }
    }
}
