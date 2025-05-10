<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaasInstallationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        $rules = [
            'server_type' => 'required',
            'purchasecode' => 'required',
            'central_domain' => ['required', 'url', function($attribute, $value, $fail) {
                if (strpos($value, 'https://') !== 0) {
                    $fail('The ' . $attribute . ' must start with https://');
                }
            }],
            //'db_prefix' => 'required',
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => ['required', 'regex:/^\S*$/'],
            'db_username' => ['required', 'regex:/^\S*$/'],
            //'db_password' => ['required', 'regex:/^\S*$/'],
        ];

        if($this->server_type==='cpanel') {
            $rules['cpanel_api_key'] = ['required', 'regex:/^\S*$/'];
            $rules['cpanel_username'] = ['required', 'regex:/^\S*$/'];
        }
        elseif($this->server_type==='plesk') {
            $rules['plesk_host'] = ['required'];
            $rules['plesk_username'] = ['required', 'regex:/^\S*$/'];
            $rules['plesk_password'] = ['required', 'regex:/^\S*$/'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'cpanel_api_key.regex' => "The :attribute must not contain any whitespace",
            'cpanel_username.regex' => "The :attribute must not contain any whitespace",
            'plesk_username.regex' => "The :attribute must not contain any whitespace",
            'plesk_password.regex' => "The :attribute must not contain any whitespace",
            'db_name.regex' => "The :attribute must not contain any whitespace",
            'db_username.regex' => "The :attribute must not contain any whitespace",
            'db_password.regex' => "The :attribute must not contain any whitespace",
            'central_domain.url' => "The URL format is invalid",
        ];
    }
}
