<?php
namespace Datlv\Kit\Traits\Controller;

use Illuminate\Http\Request;
use Validator;

/**
 * Class QuickUpdateActions
 *
 * @package Datlv\Kit\Traits\Controller
 *
 * Sử dụng:
 * 1. Có thể thêm: protected function quickUpdateAllowed($model, $attribute, $value) ==> bool
 *    Kiểm tra user hiện tại được phép quick update, ưu tiên kiểm tra trước Validator
 *    True: được phép, False: dừng update
 *    Nếu không có: cho phép tất cả (mặc định true)
 * 2. route: '.../quick_update' => ...Controller@quickUpdate
 */
trait QuickUpdateActions
{
    /**
     * Định nghĩa các attributes có thể quick update
     *    - Mổi hàng mô tả 1 attribute theo định dạng (result là tùy chọn)
     *       + attribute(string) => ['rules' => (string|array), 'label' =>(string|null), 'result' => function($model){}]
     *    - Đối với các rules database (unique, exists...) phải ghi rõ column name (thường = attribute name)
     *       + 'rules' => 'unique:users,username_column_name'
     *       + 'rules' => 'exists:states,state_column_name'
     *    - Nếu không có 'label' thì sử dụng giá trị mặc định ('artibute')
     *    - Nếu rules là array: xét quan hệ phụ thuộc giữa các attribute, Ex: 'title' phải có 'slug' unique
     *       + rules[0]: rules của attribute
     *       + rules[1]: attribute phụ thuộc(dependent)
     *       + rules[2]: closure function tạo giá trì của dependent từ giá trị của attribute
     *       + rules[3]: rules của dependent
     *           [
     *               'required|max:255',
     *               'slug',
     *               function ($title) {
     *                   return VnString::to_slug($title);
     *               },
     *               'unique:articles,slug,__ID__'
     *           ],
     *    - result: hàm lấy giá trị trả về client => 'result'
     * @return array
     */
    abstract protected function quickUpdateAttributes();

    /**
     * @param \Illuminate\Http\Request $request
     * @param mixed $model
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function quickUpdate(Request $request, $model)
    {
        if(method_exists($this, 'quickUpdateModel')){
            $model = $this->quickUpdateModel($model);
        }
        $attributes = $this->quickUpdateAttributes();
        $inputs = $request->all();
        if (isset($inputs['_attr']) && isset($attributes[$inputs['_attr']])) {
            $allowed = method_exists($this, 'quickUpdateAllowed') ?
                $this->quickUpdateAllowed($model, $inputs['_attr'], $inputs['_value']) : true;
            if ($allowed) {
                $attribute = $inputs['_attr'];
                $rules = ['_attr' => 'required|in:' . implode(',', array_keys($attributes))];
                $attribute_define = $attributes[$attribute];
                $label = empty($attribute_define['label']) ? trans('common.attribute') : $attribute_define['label'];
                if (is_string($attribute_define['rules'])) {
                    // (string) rules đơn giãn
                    $rules['_value'] = $attribute_define['rules'];
                } else {
                    // (array) rules có phụ thuộc
                    $rules['_value'] = $attribute_define['rules'][0];
                    $dependent = $attribute_define['rules'][1];
                    $inputs[$dependent] = $attribute_define['rules'][2]($inputs['_value']); //closure function
                    $rules[$dependent] = $attribute_define['rules'][3];
                }
                $this->processRule($rules, $model);
                $validator = Validator::make($inputs, $rules, [], ['_value' => $label]);
                if ($validator->passes()) {
                    $inputs[$attribute] = $inputs['_value'];
                    unset($inputs['_attr']);
                    unset($inputs['_value']);
                    $model->timestamps = false;
                    $model->fill($inputs);
                    $model->save();
                    $result = [
                        'type'    => 'success',
                        'message' => trans('common.quick_update_success', ['attribute' => $label]),
                    ];
                    if (isset($attribute_define['result'])) {
                        $result['result'] = $attribute_define['result']($model);
                    }

                    return response()->json($result);
                } else {
                    return response()->json(['type' => 'error', 'message' => $validator->messages()->first()]);
                }
            } else {
                return response()->json(['type' => 'error', 'message' => trans('common.quick_update_not_allowed')]);
            }
        } else {
            return response()->json(['type' => 'error', 'message' => trans('common.quick_update_invalid')]);
        }
    }

    /**
     * @param array &$rules
     * @param mixed $model
     */
    protected function processRule(&$rules, $model)
    {
        $search = '__ID__';
        $replace = $model->id;
        foreach ($rules as &$rule) {
            $rule = str_replace($search, $replace, $rule);
        }
    }
}
