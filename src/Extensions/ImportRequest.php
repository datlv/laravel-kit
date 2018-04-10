<?php

namespace Datlv\Kit\Extensions;
/**
 * Class ImportRequest
 * @package Datlv\Kit\Extensions
 * @author Minh Bang
 */
class ImportRequest extends Request
{
    public $trans_prefix = 'kit::import';
    protected $rules = [
        'file' => 'required|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

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
        return $this->rules;
    }

    public function messages()
    {
        return parent::messages() + [
                'mimetypes' => trans('kit::import.file_type_error'),
            ];
    }

}
