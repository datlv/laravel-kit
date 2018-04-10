<?php
if ( ! function_exists('api_route')) {
    /**
     * @param string $name
     * @param array $parameters
     * @param string $version
     * @param bool $absolute
     *
     * @return string
     */
    function api_route($name, $parameters = [], $version = 'v1', $absolute = true)
    {
        return app('Dingo\Api\Routing\UrlGenerator')->version($version)->route($name, $parameters, $absolute);
    }
}

if ( ! function_exists('kit')) {
    /**
     * @return \Datlv\Kit\Manager
     */
    function kit()
    {
        return app('kit');
    }
}

if ( ! function_exists('format_price')) {
    /**
     * @param integer $price
     * @param string $sign
     * @param string|null $wrapper
     * @param bool $empty
     * @param int $decimals
     *
     * @return null|string
     */
    function price_format($price, $sign = '', $wrapper = null, $empty = false, $decimals = 0)
    {
        if (empty($price)) {
            if ($empty) {
                $price = 0;
            } else {
                return null;
            }
        }
        $price = number_format($price, $decimals, trans('currency.dec_point'), trans('currency.thousands_sep')) . $sign;

        return $wrapper ? "<span class=\"$wrapper\">$price</span>" : $price;
    }
}

if ( ! function_exists('mb_str_prefix')) {
    /**
     * @param string $prefix
     * @param string|array $str
     * @param string $separation
     *
     * @return string
     */
    function mb_str_prefix($prefix, $str, $separation = '/')
    {
        if (empty($prefix)) {
            return $str;
        }
        if (empty($str)) {
            return $prefix;
        }
        $input      = is_string($str) ? [$str] : $str;
        $separation = is_string($separation) ? [$separation] : $separation;
        foreach ($input as &$s) {
            $sep = isset($separation[$s]) ? $separation[$s] : $separation[0];
            $s   = ($prefix ? $prefix . $sep : '') . $s;
        }

        return is_string($str) ? $input[0] : $input;
    }
}
if ( ! function_exists('mb_menu_active')) {
    /**
     * Return class active if the current request URI matches a pattern.
     *
     * @param bool|string|array $patterns
     * @param null|string $prefix
     * @param string $active_class
     * @param bool $force
     *
     * @return string
     */
    function mb_menu_active($patterns, $prefix = null, $active_class = 'active', $force = false)
    {
        if ($patterns === true || $force) {
            return $active_class;
        }
        if ((empty($patterns) || $patterns == '#') && empty($prefix)) {
            return '';
        }
        if ( ! is_array($patterns)) {
            $patterns = [$patterns];
        }
        foreach ($patterns as $pattern) {
            if (is_bool($pattern)) {
                return $pattern ? $active_class : '';
            } else {
                if (strpos($pattern, '!') === 0) {
                    $pattern = substr($pattern, 1);
                    $not     = true;
                } else {
                    $not = false;
                }
                $pattern = mb_str_prefix($prefix, $pattern, ['/', '*' => '']);
                if (app('request')->is($pattern)) {
                    return $not ? '' : $active_class;
                }
            }
        }

        return '';
    }
}

if ( ! function_exists('mb_array_to_ids')) {
    /**
     * Kiểm tra danh sách IDs và nối lại thành string
     *
     * @param array $list
     * @param string $delimiter
     *
     * @return bool|string
     */
    function mb_array_to_ids($list, $delimiter = ',')
    {
        $out = implode($delimiter, $list);

        return preg_match('/^\d(?:,\d)*$/', $out) ? $out : false;
    }
}

if ( ! function_exists('mb_array_extract')) {
    /**
     * Tách item ra khỏi array
     *
     * @param mixed $key
     * @param array $values
     * @param mixed $default
     * @param null|string $prepend
     *
     * @return mixed
     */
    function mb_array_extract($key, &$values, $default = null, $prepend = null)
    {
        if (isset($values[$key])) {
            $out = $values[$key];
            unset($values[$key]);
        } else {
            $out = $default;
        }

        return is_null($prepend) ? $out : $prepend . $out;
    }
}

if ( ! function_exists('mb_array_range')) {
    /**
     * Tạo array các số integer
     * Khác với hàm range() của PHP là index tướng ứng với value thay vì bắt đầu từ 0
     *
     * @param int $from
     * @param int $to
     *
     * @return array
     */
    function mb_array_range($from, $to)
    {
        $a = range($from, $to);

        return array_combine($a, $a);
    }
}

if ( ! function_exists('mb_array_merge')) {
    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     *
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     *                 arrays via third argument, fourth argument etc.
     *
     * @return array the merged array (the original arrays are not changed.)
     */
    function mb_array_merge($a, $b)
    {
        $args = func_get_args();
        $res  = array_shift($args);
        while ( ! empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = mb_array_merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }
}

if ( ! function_exists('mb_array_sure')) {
    /**
     * Tạo array từ string
     *
     * @param string|array $arr
     * @param string $delimiter
     *
     * @return array
     */
    function mb_array_sure($arr, $delimiter = ',')
    {
        if (is_string($arr)) {
            $arr = array_map('trim', explode($delimiter, $arr));
        }

        return is_array($arr) ? $arr : [];
    }
}

if ( ! function_exists('mb_format_bytes')) {
    /**
     * Định dạng kích thước file
     *
     * @param int $bytes Number of bytes (eg. 25907)
     * @param int $precision [optional] Number of digits after the decimal point (eg. 1)
     *
     * @return string Value converted with unit (eg. 25.3 KB)
     */
    function mb_format_bytes($bytes, $precision = 2)
    {
        $unit = ["B", "KB", "MB", "GB"];
        $exp  = floor(log($bytes, 1024)) | 0;

        return round($bytes / (pow(1024, $exp)), $precision) . ' ' . $unit[$exp];
    }
}

if ( ! function_exists('mb_date_mysql2vn')) {
    /**
     * Chuyển ngày từ (yyyy-mm-dd) thành (dd/mm/yyyy) ngày kiểu VN
     *
     * @param string $date
     * @param bool $time
     * @param string $delimiter
     *
     * @return string
     */
    function mb_date_mysql2vn($date, $time = false, $delimiter = ' | ')
    {
        if ($time) {
            list($date, $time) = explode(' ', $date);
            $time = $delimiter . $time;
        } else {
            $time = '';
        }
        $date = preg_replace('/^((19|20)\d\d)(\-)(0[1-9]|1[012])\3(0[1-9]|[12][0-9]|3[01])$/', '$5/$4/$1', $date);

        return $date . $time;
    }
}
if ( ! function_exists('mb_date_vn2mysql')) {
    /**
     * Chuyển ngày từ (dd/mm/yyyy) thành (yyyy-mm-dd) DATE type của MySQL
     *
     * @param string $date
     * @param bool $time Có thời gian
     * @param string $delimiter
     *
     * @return string
     */
    function mb_date_vn2mysql($date, $time = false, $delimiter = ' | ')
    {
        if ($time) {
            list($date, $time) = explode($delimiter, $date);
            $time = " $time";
        } else {
            $time = '';
        }
        $date = preg_replace('/^(0[1-9]|[12][0-9]|3[01])(\/)(0[1-9]|1[012])\2((19|20)\d\d)$/', '$4-$3-$1', $date);

        return $date . $time;
    }
}

if ( ! function_exists('mb_date_isvn')) {
    /**
     * Kiểm tra có phải ngày định dạng VN (dd/mm/yyyy)
     *
     * @param string $date
     *
     * @return bool
     */
    function mb_date_isvn($date)
    {
        return preg_match('/^(0[1-9]|[12][0-9]|3[01])(\/)(0[1-9]|1[012])\2((19|20)\d\d)$/', $date);
    }
}

if ( ! function_exists('mb_fn_str')) {
    /**
     * Gọi hàm từ string $params, vd: 'trans::common.test' => trans('common.test')
     * Nếu tham số là array chỉ 'fire fn' cho value, key vẫn không thay đổi, trả về array
     *
     * @param string|array $params
     * @param string $sep
     *
     * @return array|string
     */
    function mb_fn_str($params, $sep = '::')
    {
        $result = is_array($params) ? $params : [$params];
        foreach ($result as &$str) {
            if (strpos($str, $sep) !== false) {
                list($fn, $param) = explode($sep, $str, 2);

                $str = $fn($param);
            }
        }

        return is_array($params) ? $result : $result[0];
    }
}

if ( ! function_exists('mb_fn_fire')) {
    /**
     * Gọi hàm từ string $fn đối với $param
     *
     * @param  string $fn
     * @param  mixed $param
     *
     * @return mixed
     */
    function mb_fn_fire($fn, $param)
    {
        if (empty($fn)) {
            return $param;
        } else {
            if (strpos($fn, '::') !== false) {
                list($class, $method) = explode('::', $fn);

                return $class::$method($param);
            } else {
                return $fn($param);
            }
        }
    }
}

if ( ! function_exists('mb_fn_list')) {
    /**
     * Gọi hàm lấy list từ string $fn đối với $param
     *
     * @param  string $fn
     * @param  mixed $param
     * @param  mixed $empty
     *
     * @return mixed
     */
    function mb_fn_list($fn, $param, $empty)
    {
        $list = mb_fn_fire($fn, $param);

        return count($list) ? $list : $empty;
    }
}

if ( ! function_exists('mb_string_limit')) {
    /**
     * @param string $str
     * @param int $limit
     * @param string $end
     *
     * @return string
     */
    function mb_string_limit($str, $limit, $end = '...')
    {
        return $limit ? (string)kit()->s(mb_strip_all_tags($str))->truncateSafely($limit, $end) : $str;
    }
}

if ( ! function_exists('mb_button_class')) {
    /**
     * Tạo class từ $attributes
     *
     * @param  array $attributes
     * @param string $type_default
     *
     * @return string
     */
    function mb_button_class(&$attributes = [], $type_default = 'default')
    {
        $class = (isset($attributes['class']) ? $attributes['class'] . ' ' : '') . 'btn btn-';
        $class .= mb_array_extract('type', $attributes, $type_default);
        $class .= mb_array_extract('size', $attributes, '', ' btn-');

        return $class;
    }
}

if ( ! function_exists('mb_title')) {
    /**
     * Tạo class từ $attributes
     *
     * @param string $title
     * @param  array $attributes
     *
     * @return string
     */
    function mb_title($title, $attributes = [])
    {
        return $attributes ? mb_button_title($title, $attributes) : $title;
    }
}

if ( ! function_exists('mb_button_title')) {
    /**
     * Tạo class từ $attributes
     *
     * @param string $title
     * @param  array $attributes
     *
     * @return string
     */
    function mb_button_title($title, &$attributes = [])
    {
        $raw = mb_array_extract('raw', $attributes, false);
        if ($raw) {
            $title = Html::entities($title);
        }
        $icon  = mb_icon_html(mb_array_extract('icon', $attributes, ''));
        $title = empty($title) ? $icon : (empty($icon) ? $title : "{$icon} {$title}");
        if (isset($attributes['title'])) {
            $attributes['data-toggle'] = 'tooltip';
        }
        $badge = mb_array_extract('badge', $attributes, false);
        if ($badge) {
            $title .= " <span class=\"badge\">{$badge}</span>";
        }

        return $title;
    }
}

if ( ! function_exists('mb_icon_html')) {
    /**
     * Tạo class từ $attributes
     *
     * @param string $icon
     * @param  string $class
     * @param  string $tag
     *
     * @return string
     */
    function mb_icon_html($icon, $class = '', $tag = 'i')
    {
        if (empty($icon)) {
            return '';
        }
        if (substr($icon, 0, 3) == 'fa-') {
            $class .= " fa {$icon}";
        } else {
            $class .= " glyphicon glyphicon-{$icon}";
        }
        $class = trim($class);

        return "<{$tag} class=\"{$class}\"></{$tag}>";
    }
}

if ( ! function_exists('mb_attributes_addclass')) {
    /**
     * @param array $attributes
     * @param string $class
     */
    function mb_attributes_addclass(&$attributes, $class)
    {
        $attributes['class'] = (isset($attributes['class']) ? $attributes['class'] . ' ' : '') . $class;
    }
}

if ( ! function_exists('mb_html_minify')) {
    /**
     * HTML Minification
     *
     * @param string $html
     * @param array|string $except
     *
     * @return string
     */
    function mb_html_minify($html, $except = 'pre,code')
    {
        // Không minify một số tags
        if (is_string($except)) {
            $except = explode(',', $except);
        }
        $except_tags = [];
        $found       = [];
        if ($except) {
            foreach ($except as $tag) {
                $except_tags[$tag] = '#\<' . $tag . '.*\>.*\<\/' . $tag . '\>#Uis';
            }
            foreach ($except_tags as $tag => $pattern) {
                // Tìm tất cả tag
                preg_match_all($pattern, $html, $found[$tag]);
                // // Thay bằng __TAG0__, __TAG1__ ...
                $html = str_replace($found[$tag][0], array_map(function ($el) use ($tag) {
                    return "__" . strtoupper($tag) . "{$el}__";
                }, array_keys($found[$tag][0])), $html);
            }
        }

        // minify html
        $filters = [
            // remove HTML comments except IE conditions
            '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s' => '',
            // remove comments in the form /* */
            '/(?<!\S)\/\/\s*[^\r\n]*/'                            => '',
            // shorten multiple white spaces
            '/>\s{2,}</'                                          => '><',
            // shorten multiple white spaces
            '/\s{2,}/'                                            => ' ',
            // collapse new lines
            '/(\r?\n)/'                                           => '',
        ];
        $html    = preg_replace(array_keys($filters), array_values($filters), $html);

        // Trả lại các tags đã lưu
        if ($except) {
            foreach ($except as $tag) {
                $html = str_replace(array_map(function ($el) use ($tag) {
                    return "__" . strtoupper($tag) . "{$el}__";
                }, array_keys($found[$tag][0])), $found[$tag][0], $html);
            }
        }

        return $html;
    }
}
if ( ! function_exists('mb_strip_all_tags')) {
    /**
     * @param string $string
     * @param bool $remove_breaks
     *
     * @return string
     */
    function mb_strip_all_tags($string, $remove_breaks = false)
    {
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
        $string = strip_tags($string);

        if ($remove_breaks) {
            $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
        }

        return trim($string);
    }
}

if ( ! function_exists('xuuid')) {
    /**
     * @return string UUID, exp: 2c2e4067d1c92109660b8deecae1be08
     */
    function xuuid()
    {
        return md5(microtime() . mt_rand(0, 0xffff));
    }
}

if ( ! function_exists('encode_id')) {
    /**
     * Mã hóa ID
     *
     * @param integer $id
     * @param string $connection
     *
     * @return string
     */
    function encode_id($id, $connection = 'main')
    {
        return app('hashids')->connection($connection)->encode($id);
    }
}

if ( ! function_exists('decode_id')) {
    /**
     * Giải mã ID
     *
     * @param string $code
     * @param string $connection
     *
     * @return int
     */
    function decode_id($code, $connection = 'main')
    {
        return app('hashids')->connection($connection)->decode($code);
    }
}

if ( ! function_exists('check_path')) {
    /**
     * Error code: 1 => mkdir error, 2 => chmod error
     *
     * @param string $path
     * @param bool $ignore_error không 'abort' khi có lỗi
     * @param mixed $return_value giá trị trả về khi OK
     * @param int $mode path chmod
     *
     * @return mixed
     */
    function check_path($path, $ignore_error = false, $return_value = '__n0v@lu3__', $mode = 0777)
    {
        if ( ! is_dir($path) && ! mkdir($path, $mode, true)) {
            return response_error(1, trans('errors.mkdir'), $ignore_error);
        }
        if ( ! is_writable($path) && ! chmod($path, $mode)) {
            return response_error(2, trans('errors.chmod'), $ignore_error);
        }

        return $return_value === '__n0v@lu3__' ? [0, null] : $return_value;
    }
}

if ( ! function_exists('response_error')) {
    /**
     * @param integer $error
     * @param string $message
     * @param bool $ignore
     * @param int $code
     *
     * @return array
     */
    function response_error($error, $message, $ignore, $code = 400)
    {
        if ( ! $ignore && $error > 0) {
            if (Request::ajax()) {
                $message = json_encode(['error' => $message]);
                $headers = ['Content-Type' => 'application/json'];
            } else {
                $headers = [];
            }
            abort($code, $message, $headers);
        }

        return [$error, $message];
    }
}

if ( ! function_exists('response_empty_datatables')) {
    /**
     * @param string $error
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function response_empty_datatables($error = null)
    {
        $data = ($error ? ['error' => $error] : []) +
                ['draw' => 0, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []];

        return response()->json($data);
    }
}
