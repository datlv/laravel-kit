<?php

namespace Datlv\Kit\Extensions\Html;

/**
 * Class FormBuilder
 *
 * @property-read HtmlBuilder $html
 *
 * @package Datlv\Kit\Extensions\Html
 */
class FormBuilder extends \Collective\Html\FormBuilder
{
	/**
	 * @param $value
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function buttonExt($value, $attributes = [])
	{
		$value = mb_button_title($value, $attributes);

		return $this->button($value, $attributes);
	}

	/**
	 * @param string $name
	 * @param string $container
	 * @param string $callback
	 *
	 * @return string
	 */
	public function browseImage($name = 'image_id', $container = 'this', $callback = 'selectImageConfirm')
	{
		return $this->html->modalButton(
				route('image.browse'),
				trans('common.select_image'),
				[
					'label'     => trans('common.ok'),
					'container' => $container,
					'title'     => trans('common.select_image'),
					'icon'      => 'image',
					'classname' => 'modal-fullscreen',
					'callback'  => $callback,
				],
				['type' => 'primary', 'icon' => 'fa-folder-open']
			) . $this->hidden($name);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function fileField($name, $label = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes, false);
		$options['wrapper-class'] = 'form-group-fileinput';
		$element = $this->fileinput($name, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * Phân tích $attributes
	 * - Thêm các attr mặc định
	 * - Tách thành: $options (tham số) và $attributes (thuộc tính của html tag)
	 *
	 * @param  string $name
	 * @param  array $attributes
	 * @param bool $addclass
	 *
	 * @return array [0 => $attributes, 1 => $options]
	 */
	protected function parserAttributes($name, $attributes = [], $addclass = true)
	{
		$addclass = mb_array_extract('addclass', $attributes, $addclass);
		if ($addclass) {
			$attributes['class'] = 'form-control' . (empty($attributes['class']) ? '' : ' ' . $attributes['class']);
		}
		$name = str_replace('[]', '', $name);
		$attributes = array_merge(['id' => 'id-field-' . $name], $attributes);
		$options = $this->extractOptions($attributes);

		return [$attributes, $options];
	}

	/**
	 * Tách thành: $options (tham số) và $attributes (thuộc tính của html tag)
	 *
	 * @param  array $attributes
	 *
	 * @return array $options
	 */
	protected function extractOptions(&$attributes)
	{
		$options = [];
		foreach (['cols', 'addon-left', 'addon-right', 'help', 'wrapper-class', 'cancel'] as $opt) {
			if (isset($attributes[$opt])) {
				$options[$opt] = $attributes[$opt];
				unset($attributes[$opt]);
			}
		}

		return $options;
	}

	/**
	 * @param string $name
	 * @param array $options
	 *
	 * @return string
	 */
	public function fileinput($name, $options = [])
	{
		$lang_remove = trans('common.remove');
		$lang_select_file = trans('common.select_file');
		$lang_change = trans('common.change');
		$prompt = mb_array_extract('prompt', $options);
		$prompt = $prompt ? "<em class='text-gray'>{$prompt}...</em>" : '';
		$file = $this->file($name, $options);

		return <<<"ELEMENT"
<div class="fileinput fileinput-new input-group" data-provides="fileinput">
    <div class="form-control" data-trigger="fileinput">
        <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename">$prompt</span>
    </div>
    <a href="#" class="input-group-addon btn btn-danger fileinput-exists" data-dismiss="fileinput">$lang_remove</a>
    <span class="input-group-addon btn btn-info btn-file">
        <span class="fileinput-new">$lang_select_file</span>
        <span class="fileinput-exists">$lang_change</span>
        $file
    </span>
</div>
ELEMENT;
	}

	/**
	 * @param $name
	 * @param $label
	 * @param $element
	 * @param $options
	 *
	 * @return string
	 */
	protected function fieldWrapper($name, $label, $element, $options)
	{
		$cols = isset($options['cols']) ? $options['cols'] : ['col-xs-3', 'col-xs-9'];
		$is_horizontal = is_array($cols) && count($cols) > 1;
		// Add on
		$addon = [];
		foreach (['addon-left', 'addon-right'] as $ao) {
			$addon[$ao] = '';
			if ( ! empty($options[$ao])) {
				if ( ! is_array($options[$ao])) {
					$options[$ao] = [$options[$ao]];
				}
				foreach ($options[$ao] as $item) {
					$addon[$ao] .= "<span class=\"input-group-addon\">{$item}</span>";
				}
			}
		}
		if ($addon['addon-left'] || $addon['addon-right']) {
			$element = "<div class=\"input-group\">{$addon['addon-left']}{$element}{$addon['addon-right']}</div>";
		}

		if (is_array($name)) {
			$errors = session('errors');
			$message = '';
			if ($errors) {
				foreach ($name as $n) {
					$message .= ', ' . $errors->first(str_replace('[]', '', $n));
				}
			}
			$message = trim($message, ', ');
			$name = implode('_', $name);
		} else {
			$message = ($errors = session('errors')) ? $errors->first(str_replace('[]', '', $name)) : '';
		}
		// Help block

		if (empty($message)) {
			$message = empty($options['help']) ? '' : $options['help'];
			$has_error = '';
		} else {
			$has_error = ' has-error';
		}
		if ( ! empty($message)) {
			$element .= "<span class=\"help-block\">{$message}</span>";
		}

		if ($is_horizontal) {
			$label = $this->fieldLabel($name, $label, $cols[0]);
			$element = "<div class=\"{$cols[1]}\">{$element}</div>";
		} else {
			$label = $this->fieldLabel($name, $label);
		}

		// Wrapper class
		$wrapper_class = empty($options['wrapper-class']) ? '' : " {$options['wrapper-class']}";

		return "<div class=\"form-group{$has_error}{$wrapper_class}\">{$label}{$element}</div>";
	}

	/**
	 * @param $name
	 * @param $label
	 * @param string $class
	 *
	 * @return string
	 */
	protected function fieldLabel($name, $label, $class = '')
	{
		if (is_null($label)) {
			return '';
		}
		$name = str_replace('[]', '', $name);

		return "<label for=\"id-field-{$name}\" class=\"control-label {$class}\">{$label}</label>";
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param array $attributes
	 *
	 * @param string $id
	 * @param string|bool $title
	 * @param string $extra_html
	 *
	 * @return string
	 */
	public function imageField($name, $label = null, $attributes = [], $id = null, $title = false, $extra_html = '')
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes, false);
		$element = $this->selectImage($name, $attributes, $id, $title, $extra_html);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param array $attributes
	 * @param string|null $id
	 * @param string|bool $title
	 * @param string $extra_html
	 *
	 * @return string
	 */
	public function selectImage($name, $attributes = [], $id = null, $title = false, $extra_html = '')
	{
		$id = $id ?: 'image-' . time();
		$thumbnail = mb_array_extract('thumbnail', $attributes, []);
		$w = mb_array_extract('width', $thumbnail, 200);
		$h = mb_array_extract('height', $thumbnail, 200);
		$url = mb_array_extract('url', $thumbnail, null);
		$img = empty($url) ?
			'<img class="fileinput-image-thumbnail holderjs" data-src="holder.js/' . $w . 'x' . $h . '">' :
			'<img class="fileinput-image-thumbnail" src="' . $url . '">';
		$w += 8;
		$h += 16;
		$lang_remove = trans('common.remove');
		$lang_select_image = trans('common.select_image');
		$lang_change = trans('common.change');
		$html_file = $this->file($name, $attributes);

		if ($title !== false) {
			$title = $this->label("title_{$name}", trans('common.title'), ['class' => 'control-label']) .
			         '<div class="image-title">' .
			         $this->text(
				         "title_{$name}",
				         $title,
				         ['id' => "title-{$id}", 'class' => 'form-control input-sm']
			         ) .
			         '</div>';
		}

		return <<<"ELEMENT"
<div id="$id" class="image-select">
<div class="fileinput fileinput-new" data-provides="fileinput">
    <div class="fileinput-new thumbnail" style="max-width: {$w}px; max-height: {$h}px;">$img</div>
    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width:{$w}px;max-height:{$h}px;"></div>
    <div>
        <span class="btn btn-primary btn-xs btn-file">
            <span class="fileinput-new">$lang_select_image</span>
            <span class="fileinput-exists">$lang_change</span>
            $html_file
        </span>
        <a href="#" class="btn btn-danger btn-xs fileinput-exists" data-dismiss="fileinput">$lang_remove</a>
    </div>
</div>
$title
$extra_html
</div>
ELEMENT;
	}
	/**
	 * Form Fields
	 * -----------------------------------------------------------------------------------------------------------------
	 */

	/**
	 * @param null $label
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function submitField($label = null, $attributes = [])
	{
		$options = $this->extractOptions($attributes);
		$label = $label == null ? trans('app.save') : $label;
		$label = mb_button_title($label, $attributes);
		$class = mb_button_class($attributes, 'primary');
		$element = "<button type=\"submit\" class=\"{$class}\">{$label}</button>";
		$cancel = mb_array_extract('cancel', $options, []);
		if (is_array($cancel)) {
			$cancel_url = empty($cancel['url']) ? url('/') : $cancel['url'];
			$cancel_class = mb_button_class($cancel);
			$cancel_title = empty($cancel['title']) ? trans('app.cancel') : $cancel['title'];
			$element = "\n<a href=\"{$cancel_url}\" class=\"$cancel_class\">{$cancel_title}</a>{$element}";
		}
		$wrapper_class = empty($options['wrapper-class']) ? '' : " {$options['wrapper-class']}";
		$cols = mb_array_extract('cols', $options, ['col-xs-3', 'col-xs-9']);
		if (is_array($cols)) {
			list($a, $b, $c) = explode('-', $cols[0]);
			$cols[0] = "{$a}-{$b}-offset-{$c}";
			$element = "<div class=\"{$cols[1]} {$cols[0]}\">{$element}</div>";
		}

		return "<div class=\"form-actions{$wrapper_class}\"><div class=\"form-group\">{$element}</div></div>";
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param null $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function staticField($name, $label = null, $value = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes);
		$attributes['class'] = 'form-control-static' . (empty($attributes['class']) ? '' : ' ' . $attributes['class']);
		$element = "<p class=\"{$attributes['class']}\">{$value}</p>";

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param null $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function timeField($name, $label = null, $value = null, $attributes = [])
	{
		$value = $value ?: [null, null];
		if (is_string($name)) {
			$name = ["{$name}_hour", "{$name}_minute"];
		}
		list($attributes, $options) = $this->parserAttributes('time', $attributes);
		$element_hour = $this->text($name[0], $value[0], ['id' => "id-field-{$name[0]}"] + $attributes);
		$element_minute = $this->text($name[1], $value[1], ['id' => "id-field-{$name[1]}"] + $attributes);
		$element = <<<ELEMENT
<div class="input-group input-time">
  $element_hour<span class="input-group-addon">Giờ</span>
  $element_minute<span class="input-group-addon">Phút</span>
</div>
ELEMENT;

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param null $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function textField($name, $label = null, $value = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes);
		$element = $this->text($name, $value, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function passwordField($name, $label = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes);
		$element = $this->password($name, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param null $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function textareaField($name, $label = null, $value = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes);
		$element = $this->textarea($name, $value, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param string|null $label
	 * @param array $list
	 * @param mixed $value
	 * @param array $options
	 *
	 * @return string
	 */
	public function radioField($name, $label = null, $list = [], $value = null, $options = [])
	{
		$element = '';
		foreach ($list as $v => $l) {
			$element .= '<label class="radio-inline">' . $this->radio($name, $v, $v == $value) . " $l</label>";
		}

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param $list
	 * @param null $value
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function selectMultipleField($name, $label = null, $list = [], $value = null, $attributes = [])
	{
		$attributes['multiple'] = true;

		return $this->selectField($name, $label, $list, $value, $attributes);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param array|\Illuminate\Support\Collection $list
	 * @param null $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function selectField($name, $label = null, $list = [], $value = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes);
		$element = $this->select($name, $list, $value, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * Mở rộng chức năng cho select
	 * 1. Prompt: thông qua $options['prompt'] và $options['prompt-value']
	 * 2. Render Html Select tag mở rộng
	 *  $list[] = [
	 *      'value' => '',
	 *      'text' => '',
	 *      'attributes' => ['a' => '', 'b' => '']
	 *  ]
	 *
	 * @param  string $name
	 * @param  array $list
	 * @param  string $selected
	 * @param array $selectAttributes
	 * @param array $optionsAttributes
	 * @param array $optgroupsAttributes
	 *
	 * @return string
	 */
	public function select(
		$name,
		$list = [],
		$selected = null,
		array $selectAttributes = [],
		array $optionsAttributes = [],
		array $optgroupsAttributes = []
	) {
		if ($list instanceof \Illuminate\Support\Collection) {
			$list = $list->all();
		}
		if ($this->isSelectExt($list)) {
			$items = [];
			$search = [];
			$replace = [];
			foreach ($list as $item) {
				$items[$item['value']] = $item['text'];
				$attributes = isset($item['attributes']) ? $item['attributes'] : [];
				$value = 'value="' . e($item['value']) . '"';
				$search[] = $value;
				$replace[] = $value . ' ' . $this->html->rawAttributes($attributes);
			}
			$html = $this->selectWithPrompt($name, $items, $selected, $selectAttributes, $optionsAttributes, $optgroupsAttributes);

			return str_replace($search, $replace, $html);
		} else {
			return $this->selectWithPrompt($name, $list, $selected, $selectAttributes, $optionsAttributes, $optgroupsAttributes);
		}
	}

	/**
	 * Có phải là select mở rộng
	 *
	 * @param array $list
	 *
	 * @return bool
	 */
	protected function isSelectExt($list)
	{
		$item = current($list);

		return $item && is_array($item) && isset($item['value']) && isset($item['text']);
	}

	/**
	 * @param string $name
	 * @param array $list
	 * @param string $selected
	 * @param array $selectAttributes
	 * @param array $optionsAttributes
	 * @param array $optgroupsAttributes
	 *
	 * @return string
	 */
	protected function selectWithPrompt($name, $list, $selected, $selectAttributes, $optionsAttributes, $optgroupsAttributes)
	{
		$prompt = mb_array_extract('prompt', $selectAttributes);
		if (is_string($prompt)) {
			$prompt_value = mb_array_extract('prompt-value', $selectAttributes, '');
			$list = [$prompt_value => $prompt] + $list;
		}

		return parent::select($name, $list, $selected, $selectAttributes, $optionsAttributes, $optgroupsAttributes);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param null $checked
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function checkboxField($name, $label = null, $checked = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes, false);
		mb_attributes_addclass($attributes, 'no-switch');
		$value = mb_array_extract('value', $attributes, 1);
		$element = $this->checkbox($name, $value, $checked, $attributes);
		if (isset($options['cols']) && $options['cols'] === false) {
			return "<div class=\"checkbox\"><label>{$element} {$label}</label></div>";
		} else {
			$text = mb_array_extract('text', $attributes, null, ' ');
			$element = "<div class=\"checkbox\"><label>{$element}{$text}</label></div>";

			return $this->fieldWrapper($name, $label, $element, $options);
		}
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param $checked
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function switchField($name, $label = null, $checked = false, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes, false);
		if ($label === '') {
			$label = trans('app.activate');
		}
		$value = mb_array_extract('value', $attributes, 1);
		$element = $this->html->switchElement($name, $value, $checked, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param array $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function daterangeField($name, $label = null, $value = [], $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes);
		$element = $this->daterange($name, $value, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * @param $name
	 * @param array $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function daterange($name, $value = [], $attributes = [])
	{
		$attributes['placeholder'] = 'dd/mm/yyyy';
		$value_start = isset($value[0]) ? $value[0] : null;
		$value_end = isset($value[1]) ? $value[1] : null;
		$element = $this->text($name . '_start', $value_start, $attributes);
		$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-arrow-right"></span></span>';
		$element .= $this->text($name . '_end', $value_end, $attributes);

		return '<div class="input-daterange input-group">' . $element . '</div>';
	}

	/**
	 * @param $name
	 * @param string $label
	 * @param string $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function datepickerField($name, $label = null, $value = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes);
		$element = $this->date($name, $value, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * Select date field
	 *
	 * @param $name
	 * @param string|null $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function date($name, $value = null, $attributes = [])
	{
		mb_attributes_addclass($attributes, 'datepicker');
		$attributes['placeholder'] = 'dd/mm/yyyy';
		$element = $this->text($name, $value, $attributes);
		$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';

		return '<div class="input-group">' . $element . '</div>';
	}

	/**
	 * @param $name
	 * @param string $label
	 * @param string $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function datetimepickerField($name, $label = null, $value = null, $attributes = [])
	{
		list($attributes, $options) = $this->parserAttributes($name, $attributes);
		$element = $this->datetime($name, $value, $attributes);

		return $this->fieldWrapper($name, $label, $element, $options);
	}

	/**
	 * Select date field
	 *
	 * @param $name
	 * @param string|null $value
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function datetime($name, $value = null, $attributes = [])
	{
		mb_attributes_addclass($attributes, 'datetimepicker');
		$attributes['placeholder'] = 'dd/mm/yyyy H:i';
		$element = $this->text($name, $value, $attributes);
		$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';

		return '<div class="input-group">' . $element . '</div>';
	}
}
