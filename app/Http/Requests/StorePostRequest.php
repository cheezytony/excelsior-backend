<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'preview' => 'required',
            'body' => 'required',
            'tags' => 'required|array|min:3',
            'featured_image' => 'nullable|file|image',
            'topic' => 'required',
        ];
    }
}
