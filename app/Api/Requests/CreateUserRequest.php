<?php

namespace Api\Requests;

use Dingo\Api\Http\FormRequest;
use Api\Requests\UpdateUserRequest;

class CreateUserRequest extends FormRequest
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
        $updateUserRequest = new UpdateUserRequest();
        $rules = $updateUserRequest->rules();
        foreach ($rules as &$rule) {
            if (strpos($rule, 'required') === false) {
                $rule = 'required|' . $rule;
            }
        }
        return $rules;
    }
}
