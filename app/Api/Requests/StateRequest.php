<?php

namespace Api\Requests;

use Dingo\Api\Http\FormRequest;

class StateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; //TODO update this to check if the user is able to add a state
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:states',
            'short_name' => 'required|max:10|unique:states',
        ];
    }
}
