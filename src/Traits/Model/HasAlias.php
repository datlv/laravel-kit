<?php
namespace Datlv\Kit\Traits\Model;

/**
 * Class HasAliasTrait
 * Class sử dụng phải có 'public static function aliases()' =>array
 *
 * @package App\Traits
 */
trait HasAlias
{
    /**
     * @var array Cache of aliases()
     */
    protected $_aliases;

    /**
     * Định nghĩa các aliases
     *
     * @return array
     */
    abstract protected function aliases();

    /**
     * @param mixed $params
     * @param mixed $code
     * @param mixed $default
     *
     * @return mixed|false
     */
    public function itemAlias($params = null, $code = null, $default = false)
    {
        $this->_aliases = $this->_aliases ?: $this->aliases();
        if ($params === null) {
            return $this->_aliases;
        } else {
            if (is_array($params)) {
                $type = $params[0];
                $code = isset($params[1]) ? $params[1] : $code;
                $default = isset($params[2]) ? $params[2] : $default;
            } else {
                $type = $params;
            }
            if (isset($this->_aliases[$type])) {
                if ($code === null) {
                    return $this->_aliases[$type];
                } else {
                    return isset($this->_aliases[$type][$code]) ? $this->_aliases[$type][$code] : $default;
                }
            } else {
                return $default;
            }
        }
    }
}
