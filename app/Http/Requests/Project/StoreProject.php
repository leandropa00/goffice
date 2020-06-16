<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\CoreRequest;

class StoreProject extends CoreRequest
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
            'project_name' => 'required',
            'start_date' => 'required',
            'hours_allocated' => 'nullable|numeric',
            'user_id.0' => 'required'
        ];

        if (!$this->has('without_deadline')) {
            $rules['deadline'] = 'required';
        }
        if ($this->project_budget != '') {
            $rules['project_budget'] = 'numeric';
            $rules['currency_id'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'user_id.0.required' => __('messages.atleastOneValidation')
        ];
    }
}
