<?php

namespace Datlv\Kit\Extensions;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Request
 *
 * @package Datlv\Kit\Extensions
 */
abstract class Request extends FormRequest
{
    /**
     * @var string
     */
    protected $trans_prefix = '';

    /**
     * Rules của các attributes: ['attr1' => 'rules1',...]
     * Riêng rule unique phải để cuối,
     * vd: min:4|unique:articles, KHÔNG ĐƯỢC: unique:articles|min:4
     *
     * @var array
     */
    protected $rules = [];

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
     * Auto Custom attributes for validator errors.
     * Điều kiện:
     * - Khai báo $this->rules, chỉ các attributes có trong $this->rules mới có tác dụng
     * - Khai báo $this->trans_prefix, dùng translate các attributes
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = [];
        if ($this->rules && $this->trans_prefix) {
            foreach (array_keys($this->rules) as $attribute) {
                $attributes[$attribute] = trans("{$this->trans_prefix}.{$attribute}");
            }
        }

        return $attributes;
    }
}
