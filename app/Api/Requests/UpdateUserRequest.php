<?php

namespace Api\Requests;

use Dingo\Api\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateUserRequest extends FormRequest
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
        $rules = [
            'first_name' => 'max:255',
            'last_name'=> 'max:255',
            'email' => 'email|max:255|unique:users',
            'password' => 'min:4',
        ];
        $uuid = $this->route('uuid');
        if (!empty($uuid)) {
            $rules['email'] .= ',email,' . $uuid . ',uuid';
        }
        return $rules;
    }
}
