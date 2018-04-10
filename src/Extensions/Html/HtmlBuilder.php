<?php

namespace Datlv\Kit\Extensions\Html;

/**
 * Class HtmlBuilder
 *
 * @package Datlv\Kit\Extensions\Presenter
 */
class HtmlBuilder extends \Collective\Html\HtmlBuilder
{
    const BUTTON_LINK = 'link';
    const BUTTON_LINK_NEWTAB = 'link-newtab';
    const BUTTON_MODAL = 'modal';
    const BUTTON_MODAL_LARGE = 'modal-large';
    const BUTTON_MODAL_SMALL = 'modal-small';
    const BUTTON_DISABLED = 'disabled';

    /**
     * @param array $attributes
     * @param bool $raw Không excap
     *
     * @return string
     */
    public function attributes($attributes, $raw = false)
    {
        return $raw ? $this->rawAttributes($attributes) : parent::attributes($attributes);
    }

    /**
     * Không escape các attribute value
     *
     * @param $attributes
     *
     * @return string
     */
    public function rawAttributes($attributes)
    {
        $html = [];

        foreach ((array)$attributes as $key => $value) {
            if (!is_null($value)) {
                $html[] = $this->rawAttributeElement($key, $value);;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function rawAttributeElement($key, $value)
    {
        return is_null($value) ? null : (is_numeric($key) ? $value : $key) . '="' . $value . '"';
    }

    /**
     * @param int $count
     * @param string $type
     *
     * @return string
     */
    public function formatCount($count, $type = 'danger')
    {
        if ($count) {
            return $type ? "<span class=\"badge badge-{$type}\">{$count}</span>" : $count;
        } else {
            return '';
        }
    }

    /**
     * @param array $pieces
     * @param array $options
     *
     * @return string
     */
    public function implode($pieces, $options = [])
    {
        $separator = mb_array_extract('separator', $options, '<br>');
        $before = mb_array_extract('before', $options, '');
        $after = mb_array_extract('after', $options, '');

        return $pieces ? ($before . implode($separator, $pieces) . $after) : '';
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator $results
     * @param string $name
     * @param array $appends
     *
     * @return string|null
     */
    public function pagination($results, $name, $appends = [])
    {
        if ($results->hasPages()) {
            $msg = trans(
                'pagination.results',
                [
                    'first' => ($results->currentPage() - 1) * $results->perPage() + 1,
                    'last'  => min($results->currentPage() * $results->perPage(), $results->total()),
                    'total' => $results->total(),
                    'name'  => $name,
                ]
            );

            return <<<"HTML"
<div class="pagination-block">
    <div class="pull-left">{$results->appends($appends)->links()}</div>
    <div class="pull-right results">{$msg}</div>
</div>
HTML;
        } else {
            return null;
        }
    }

    /**
     * @param string $str
     * @param string $class
     * @param bool $last
     * @param string $delimiter
     *
     * @return string
     */
    public function twoPart($str, $class = 'text-danger', $last = false, $delimiter = ' ')
    {
        $pos = $last ? strrpos($str, $delimiter) : strpos($str, $delimiter);
        if ($pos === false) {
            return $str;
        } else {
            $str_first = substr($str, 0, $pos);
            $str_last = substr($str, $pos + strlen($delimiter));
            if (!($str_first && $str_last)) {
                return $str_first . $str_last;
            }
            $class = $class ? " class=\"{$class}\"" : '';

            return $last ? "{$str_first} <span{$class}>{$str_last}</span>" : "<span{$class}>{$str_first}</span> {$str_last}";
        }
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function selectLang($url = '/locale')
    {
        $locales = array_keys(config('locale.all'));
        $list = '';
        foreach ($locales as $locale) {
            $active = config('app.locale') == $locale ? ' active' : '';
            $list .= "<a href=\"{$url}/{$locale}\" class=\"{$locale}{$active}\"></a>";
        }

        return "<div class=\"lang\">$list</div>";
    }

    /**
     * @param string $url
     * @param string $type
     * @param string $icon
     * @param string $label
     * @param int|null $count
     *
     * @return string
     */
    public function buttonBig($url, $type, $icon, $label, $count = null)
    {
        $icon = mb_icon_html($icon);
        $badge = $count ? "<span class=\"badge\">{$count}</span>" : '';

        return <<<"HTML"
<a href="{$url}" class="btn btn-{$type} btn-lg btn-big" role="button">{$icon}<br/>{$label}{$badge}</a>
HTML;
    }

    /**
     * @param string $title
     * @param string $label
     * @param string $label_type
     * @param mixed $number
     * @param array $stats
     * @param string $detail
     *
     * @return string
     */
    public function statsIBox($title, $label, $label_type, $number, $detail, $stats = [])
    {
        $stats_present = '';
        if ($number && count($stats)) {
            foreach ($stats as $stat) {
                $icon = empty($stat['icon']) ? '' : ' ' . mb_icon_html($stat['icon'], '', 'i');
                $tooltip = empty($stat['title']) ? '' : 'data-toggle="tooltip" title="' . $stat['title'] . '"';
                $stats_present .= "<div class=\"stat-percent font-bold text-{$stat['type']}\" {$tooltip}>{$stat['number']}{$icon}</div>";
            }
        }

        return <<<"HTML"
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <span class="label label-{$label_type} pull-right">{$label}</span>
        <h5>{$title}</h5>
    </div>
    <div class="ibox-content">
        <h1 class="no-margins">{$number}</h1>
        $stats_present
        <small>{$detail}</small>
    </div>
</div>
HTML;
    }

    /**
     * @param string $type
     * @param string $icon
     * @param int $number
     * @param string $label
     * @param string $url
     * @param null|string $link_label
     *
     * @return string
     */
    public function statsHuge($type, $icon, $number, $label, $url = '#', $link_label = null)
    {
        $link_label = $link_label ?: trans('common.view_detail');

        return <<<"HTML"
<div class="panel panel-{$type}">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-3"><i class="fa fa-{$icon} fa-5x"></i></div>
            <div class="col-xs-9 text-right">
                <div class="huge">{$number}</div>
                <div>{$label}</div>
            </div>
        </div>
    </div>
    <a href="{$url}">
        <div class="panel-footer">
            <span class="pull-left">{$link_label}</span>
            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
            <div class="clearfix"></div>
        </div>
    </a>
</div>
HTML;
    }

    /**
     * @param       $name
     * @param       $value
     * @param       $checked
     * @param array $attributes
     *
     * @return mixed
     */
    public function switchElement($name, $value, $checked, $attributes = [])
    {
        $attributes['data-on-text'] = mb_array_extract('yes', $attributes, trans('common.yes'));
        $attributes['data-off-text'] = mb_array_extract('no', $attributes, trans('common.no'));
        // size = '', 'mini', 'small', 'large'
        $attributes['data-size'] = mb_array_extract('size', $attributes, '');
        $attributes['data-wrapper-class'] = mb_array_extract('wrapper', $attributes, 'wrapper');

        return app('form')->checkbox($name, $value, $checked, $attributes);
    }

    /**
     * @param $items
     *
     * @return string
     */
    public function breadcrumb($items)
    {
        if (empty($items)) {
            return '';
        }
        end($items);
        $last = key($items) == '#' ? array_pop($items) : null;
        $html = '<ol class="breadcrumb">';
        foreach ($items as $url => $label) {
            $html .= "<li><a href=\"{$url}\">{$label}</a></li>";
        }
        if ($last) {
            $html .= "<li class=\"active\">{$last}</li>";
        }
        $html .= '</ol>';

        return $html;
    }

    /**
     * @param        $titles
     * @param string $default_type
     * @param array $translate
     * @param array $types
     *
     * @return string
     */
    public function formatLabels($titles, $default_type = 'primary', $translate = [], $types = [])
    {
        $out = '';
        foreach ($titles as $key => $title) {
            $title = isset($translate[$title]) ? $translate[$title] : $title;
            $type = isset($types[$key]) ? $types[$key] : $default_type;
            $out .= "<span class=\"label label-{$type}\">{$title}</span>\n";
        }

        return $out;
    }

    /**
     * @param      $mimetype
     * @param null $unknow
     *
     * @return null|string
     */
    public function formatMimetype($mimetype, $unknow = null)
    {
        if ($unknow == null) {
            $unknow = trans('common.unknow');
        }
        $mimes = new \Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser();
        $out = $mimes->guess($mimetype);
        if (empty($out)) {
            $out = '<span class="text-danger">' . $unknow . '</span>';
        } else {
            $out = strtoupper($out);
        }

        return $out;
    }

    /**
     * Lấy tham số cho html link
     *
     * @param array|string $url
     * @param string $title
     * @param array $attributes
     * @param array $params
     *
     * @return array
     */
    protected function linkParams($url, $title = '', $attributes = [], $params = [])
    {
        if (is_array($url)) {
            $title = isset($url[1]) ? $url[1] : $title;
            $attributes = isset($url[2]) ? $url[2] : $attributes;
            $params = isset($url[3]) ? $url[3] : $params;
            $url = $url[0];
        }

        return [$url, $title, $attributes, $params];
    }

    /**
     * @param array|string $url
     * @param string $title
     * @param array $attributes
     * @param array $params
     *
     * @return string
     */
    public function linkExt($url, $title = '', $attributes = [], $params = [])
    {
        list($url, $title, $attributes, $params) = $this->linkParams($url, $title, $attributes, $params);
        $title = mb_button_title($title, $attributes);
        foreach ($params as $key => $value) {
            $attributes["data-{$key}"] = $value;
        }
        $active = mb_array_extract('active', $attributes, false);
        if ($active === '*') {
            $path = ltrim(parse_url($url, PHP_URL_PATH), '/');
            $active = app('request')->is("{$path}*");
        } else {
            $active = (is_bool($active) && $active) || app('request')->url() === $url;
        }
        if ($active) {
            mb_attributes_addclass($attributes, 'active');
        }

        return "<a href=\"{$url}\"" . $this->attributes($attributes) . ">{$title}</a>";
    }

    /**
     * @param mixed $id         model ID
     * @param mixed $value      giá trị hiện tại
     * @param array $params     tham số
     *                          Params gồm:
     *                          - 'attr': attribute name
     *                          - 'title': tiêu đề popover
     *                          - 'placement': top, bottom, left, right(default)
     *                          - 'class': input element class
     *                          - 'label': là link label thay cho value
     *                          - 'null_label': label của link khi value = null thay vì value, mặc định: trans('common.quick_update_null_value')
     *                          - 'null_class': thêm css class khi value = null, mặc định: 'text-null'
     * @param array $attributes html attribute của link
     * @param string|null $url
     *
     * @return string
     */
    public function linkQuickUpdate($id, $value, $params, $attributes = [], $url = null)
    {
        $label = mb_array_extract('label', $params, $value);
        $label = $label ?: mb_array_extract('null_label', $params, trans('common.quick_update_null_label'));
        mb_attributes_addclass($attributes,
            'quick-update' . ($value ? '' : ' ' . mb_array_extract('null_class', $params, 'text-gray')));
        foreach ($params as $k => $v) {
            $attributes["data-qu_{$k}"] = $v;
        }
        $url = $url ?: "#{$id}";

        return "<a href=\"{$url}\"" . $this->attributes($attributes) . " data-qu_value=\"{$value}\">{$label}</a>";
    }

    /**
     * @param        $url
     * @param string $title
     * @param array $attributes
     * @param array $params
     *
     * @return mixed
     */
    public function linkButton($url, $title = '', $attributes = [], $params = [])
    {
        list($url, $title, $attributes, $params) = $this->linkParams($url, $title, $attributes, $params);
        $attributes['class'] = mb_button_class($attributes);

        return $this->linkExt($url, $title, $attributes, $params);
    }

    /**
     * @param array $buttons
     *
     * @return string
     */
    public function linkButtons($buttons = [])
    {
        $html = '';
        foreach ($buttons as $btn) {
            $html .= $this->linkButton($btn);
        }

        return $html;
    }

    /**
     * @param       $url
     * @param       $title
     * @param       $params
     * @param array $attributes
     *
     * @return mixed
     */
    public function modalLink($url, $title, $params, $attributes = [])
    {
        $attributes['class'] = (isset($attributes['class']) ? $attributes['class'] . ' ' : '') . 'modal-link';

        return $this->linkExt($url, $title, $attributes, $params);
    }

    /**
     * @param       $url
     * @param       $title
     * @param       $params
     * @param array $attributes
     *
     * @return mixed
     */
    public function modalButton($url, $title, $params, $attributes = [])
    {
        $attributes['class'] = (isset($attributes['class']) ? $attributes['class'] . ' ' : '') . 'modal-link';

        return $this->linkButton($url, $title, $attributes, $params);
    }

    /**
     * @param string $route_prefix
     * @param array $params
     * @param string $title
     * @param string $name
     * @param array $options
     *
     * @return string
     */
    public function tableActions($route_prefix, $params, $title, $name, $options = [])
    {
        $options = $options + [
                'renderPreview' => false,
                'renderDelete'  => 'link',
                'renderEdit'    => 'modal',
                'renderShow'    => 'modal',
                'titleEdit'     => null,
                'heightEdit'    => null,
                'heightShow'    => null,
                'template'      => ':preview:show:edit:delete',
            ];
        $preview = $this->tableButton(
            $options['renderPreview'] ? route("{$route_prefix}.preview", $params) : '#',
            [
                'title' => trans('common.object_details_view', ['name' => $name]),
                'icon'  => 'eye-open',
            ],
            [
                'icon'   => 'eye-open',
                'type'   => 'primary',
                'title'  => trans('common.preview'),
                'target' => $options['renderPreview'] == 'link' ? '_blank' : '_self',
            ],
            $options['renderPreview']
        );
        $show = $this->tableButton(
            $options['renderShow'] ? route("{$route_prefix}.show", $params) : '#',
            [
                'title'  => trans('common.object_details_view', ['name' => $name]),
                'icon'   => 'list',
                'height' => $options['heightShow'],
            ],
            ['icon' => 'list', 'type' => 'info', 'title' => trans('common.detail')],
            $options['renderShow']
        );
        $edit = $this->tableButton(
            $options['renderEdit'] ? route("{$route_prefix}.edit", $params) : '#',
            [
                'title'  => $options['titleEdit'] ?: trans('common.update_object', ['name' => $name]),
                'label'  => trans('common.save_changes'),
                'icon'   => 'edit',
                'height' => $options['heightEdit'],
            ],
            ['icon' => 'edit', 'type' => 'success', 'title' => trans('common.edit')],
            $options['renderEdit']
        );
        $delete = $this->tableButton(
            $options['renderDelete'] ? route("{$route_prefix}.destroy", $params) : '#',
            ['title' => $title],
            ['icon' => 'trash', 'type' => 'danger', 'title' => trans('common.delete'), 'class' => 'delete-link'],
            $options['renderDelete']
        );

        return strtr(
            $options['template'],
            [':preview' => $preview, ':show' => $show, ':edit' => $edit, ':delete' => $delete]
        );
    }

    /**
     * @param        $url
     * @param        $params
     * @param        $attributes
     * @param string $show link, modal, modal-[width], disabled
     *
     * @return mixed
     */
    public function tableButton($url, $params, $attributes, $show = 'modal')
    {
        if ($show === false) {
            return '';
        }
        $attributes = $attributes + ['size' => 'xs', 'icon' => false, 'raw' => false];
        switch ($show) {
            case self::BUTTON_LINK_NEWTAB:
                return $this->linkButton($url, '', $attributes + ['target' => '_blank'], $params);
                break;
            case self::BUTTON_LINK:
                return $this->linkButton($url, '', $attributes, $params);
                break;
            case self::BUTTON_DISABLED:
                mb_attributes_addclass($attributes, 'disabled');

                return $this->linkButton('#', '', $attributes, $params);
                break;
            default:
                if (strpos($show, '-') !== false) {
                    $params['width'] = last(explode('-', $show));
                }

                return $this->modalButton($url, '', $params, $attributes);
        }
    }

    /**
     * @param             $value
     * @param string $label
     * @param bool $disabled
     * @param string|null $class_yes
     * @param string|null $class_no
     * @param array $attributes
     *
     * @return string
     */
    public function yesNo($value, $label = '', $disabled = true, $class_yes = null, $class_no = null, $attributes = [])
    {
        $class_yes = $class_yes ?: 'glyphicon glyphicon-ok-circle text-success';
        $class_no = $class_no ?: 'glyphicon glyphicon-remove-circle text-danger';
        $class = ($value ? $class_yes : $class_no) . ($disabled ? ' disabled' : '');
        $attributes = $this->attributes($attributes);

        return "<span class=\"$class\" {$attributes}>$label</span>";
    }

    /**
     * @param        $value
     * @param null $label_yes
     * @param null $label_no
     * @param string $class_yes
     * @param string $class_no
     *
     * @return mixed
     */
    public function yesNoLabel($value, $label_yes = null, $label_no = null, $class_yes = null, $class_no = null)
    {
        $class_yes = $class_yes ?: 'label label-success';
        $class_no = $class_no ?: 'label label-danger';
        $label = $value ? ($label_yes ?: trans('common.active')) : ($label_no ?: trans('common.inactive'));

        return $this->yesNo($value, $label, true, $class_yes, $class_no);
    }

    /**
     * @param        $value
     * @param        $url
     * @param string $title
     *
     * @return string
     */
    public function yesNoLink($value, $url, $title = '')
    {
        $text = $this->yesNo($value, '', false);

        return "<a href=\"{$url}\" class=\"post-link\" data-toggle=\"tooltip\" title=\"{$title}\">{$text}</a>";
    }

    /**
     * @param \Carbon\Carbon $datetime
     * @param string $template
     * @param string $format_date
     * @param string $format_time
     *
     * @return string
     */
    public function datetime($datetime, $template = null, $format_date = 'd/m/Y', $format_time = 'H:i')
    {
        $template = $template ?: '<code>:date | :time</code>';
        list($date, $time) = explode('|', $datetime->format("$format_date|$format_time"), 2);

        return strtr($template, [':date' => $date, ':time' => $time]);
    }

    /**
     * @param      $parent
     * @param      $icon
     * @param      $title
     * @param      $id
     * @param      $items
     * @param bool $active
     * @param null $type
     *
     * @return string
     */
    public function panelCollapse($parent, $icon, $title, $id, $items, $active = false, $type = null)
    {
        $type = $type ?: ($active ? 'primary' : 'default');
        $icon = mb_icon_html($icon);
        $active = $active ? 'in' : '';
        $trs = '';
        foreach ($items as $item) {
            $item_icon = mb_array_extract('icon', $item, '');
            $item_icon_class = mb_array_extract('icon_type', $item, '', 'text-');
            $item_icon = mb_icon_html($item_icon, $item_icon_class);
            $trs .= "<tr><td><a href=\"{$item['url']}\">{$item_icon}{$item['title']}</a></td></tr>";
        }

        return <<<"HEADING"
<div class="panel panel-{$type}">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#{$parent}" href="#{$id}">{$icon}{$title}</a>
        </h4>
    </div>
    <div id="{$id}" class="panel-collapse collapse {$active}">
        <div class="panel-body">
            <table class="table">{$trs}</table>
        </div>
    </div>
</div>
HEADING;
    }

    /**
     * @param $items
     *
     * @return string
     */
    public function listgroup($items)
    {
        $list = '';
        $linked = is_array($items[0]) && isset($items[0]['url']);
        if ($linked) {
            $container = 'div';
            $tag = 'a';
        } else {
            $container = 'ul';
            $tag = 'li';
        }
        foreach ($items as $item) {
            if (is_string($item)) {
                $item = ['label' => $item];
            }
            if (!isset($item['visible']) || $item['visible']) {
                $href = $linked ? "href=\"{$item['url']}\" " : '';
                $active = empty($item['active']) ? '' : ' active';
                $disabled = empty($item['disabled']) ? '' : ' disabled';
                $badge = isset($item['badge']) ? "<span class=\"badge\">{$item['badge']}</span>" : '';
                $list .= "<{$tag} {$href}class=\"list-group-item{$active}{$disabled}\">{$badge}{$item['label']}</{$tag}>";
            }
        }

        return "<{$container} class=\"list-group\">{$list}</{$container}>";
    }

    /**
     * @param $label
     * @param $icon
     * @param $content
     * @param string $type
     * @param bool $body_class
     * @param array $buttons
     *
     * @return string
     */
    public function panel($label, $icon, $content, $type = 'default', $body_class = false, $buttons = [])
    {
        $icon = mb_icon_html($icon);
        $content = $body_class ? "<div class=\"panel-body\">{$content}</div>" : $content;
        $btns = $this->linkButtons($buttons);
        $btns = $btns ? "<div class=\"pull-right\">{$btns}</div>" : '';

        return <<<"PANEL"
<div class="panel panel-{$type}">
    <div class="panel-heading clearfix">
        <h3 class="panel-title">{$icon} {$label}</h3>{$btns}
    </div>
    {$content}
</div>
PANEL;
    }

    /**
     * @param $label
     * @param $icon
     * @param $items
     * @param string $type
     * @param array $buttons
     *
     * @return mixed
     */
    public function panelListgroup($label, $icon, $items, $type = 'default', $buttons = [])
    {
        return $this->panel($label, $icon, $this->listgroup($items), $type, false, $buttons);
    }

    /**
     * @param string $icon
     * @param string $count
     * @param string $title
     * @param string $color gray, white, navy, blue, lazur, yellow, red, black
     * @param string $url
     * @param string $style
     *
     * @return string
     */
    public function dashboardWidget($icon, $count, $title = null, $color = 'navy', $url = null, $style = 'style1')
    {
        /**
         * - Chỉ sử dụng icon fa-
         * - Có $title icon = 5x, ngược lại 3x
         */
        $icon = mb_icon_html('fa-' . $icon, $title ? 'fa-5x' : 'fa-3x');
        $icon_cell_size = $title ? 4 : 3;
        $title = $title ? "<span>$title</span>" : '';

        return ($url ? "<a href=\"$url\">" : '') . '
<div class="widget ' . $style . ' ' . $color . '-bg">
    <div class="row' . ($title ? '' : ' vertical-align') . '">
        <div class="col-xs-' . $icon_cell_size . '">' . $icon . '</div>
        <div class="col-xs-' . (12 - $icon_cell_size) . ' text-right">
            ' . $title . '
            <h2 class="font-bold">' . $count . '</h2>
        </div>
    </div>
</div>' . ($url ? '</a>' : '');
    }

    /**
     * @param array $widgets
     *
     * @return string
     */
    public function dashboardWidgets($widgets = [])
    {
        $html = '';
        foreach ($widgets as $widget) {
            $html .= call_user_func_array([$this, 'dashboardWidget'], $widget);
        }

        return $html;
    }
}
