<?php

namespace GetCandy\Http\Requests\Api\Categories;

use GetCandy\Http\Requests\Api\FormRequest;
use GetCandy\Api\Categories\Models\Category;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return $this->user()->can('create', Category::class);
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
            'attributes.*.value' => 'required|unique_category_attribute:name',
            'routes.*.slug' => 'required|unique_route',
        ];
    }

    public function messages()
    {
        return [
            'attributes.*.value.unique_category_attribute' => 'The name must be unique',
            'routes.*.slug.unique_route' => 'The slug must be unique',
        ];
    }
}