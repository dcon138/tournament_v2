<?php

namespace Api\Requests;

use Dingo\Api\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; //TODO update this to check if the user is able to add a client
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_group_id' => 'required|exists:client_groups,uuid,deleted_at,NULL',
            'name' => 'required|max:255|unique:clients',
            'short_name' => 'max:100',
            'abn' => 'numeric|unique:clients',
            'primary_contact_id' => 'required|exists:users,uuid,deleted_at,NULL',
            'address' => 'max:255',
            'address2' => 'max:255',
            'state_id' => 'exists:states,uuid,deleted_at,NULL',
            'suburb' => 'max:255',
            'postcode' => 'max:16',
            'bank_details' => 'max:1000',
        ];
    }
}
