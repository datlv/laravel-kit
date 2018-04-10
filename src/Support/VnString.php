<?php
namespace Datlv\Kit\Support;

class VnString
{
    /**
     * @param string $filename
     * @param string $str
     * @param string $except
     * @return string
     */
    public static function addstr_filename($filename, $str, $except = 'main')
    {
        if ($str === $except) {
            return $filename;
        } else {
            $pos = strrpos($filename, '.');
            $name = substr($filename, 0, $pos) . "-$str";
            $ext = substr($filename, $pos + 1);
            return "{$name}.{$ext}";
        }
    }

    /**
     * Chuyển tên file thành dạng slug
     *
     * @param string $filename
     * @param bool $time thêm datetime vào tên file
     * @return string
     */
    public static function slug_filename($filename, $time = false)
    {
        $pos = strrpos($filename, '.');
        $name = self::to_slug(substr($filename, 0, $pos)) . ($time ? '-' . date('H-i-s-d-m-Y') : '');
        $ext = strtolower(substr($filename, $pos + 1));

        return "{$name}.{$ext}";
    }

    /**
     * Mã hóa tên file
     *
     * @param string $filename
     * @return string
     */
    public static function hash_filename($filename)
    {
        $pos = strrpos($filename, '.');
        $name = md5(time() . $filename);
        $ext = strtolower(substr($filename, $pos + 1));

        return "{$name}.{$ext}";
    }

    /**
     * @param $str
     * @param bool $vietnamese
     * @param bool $special
     * @param bool $accent
     * @return mixed|string
     */
    public static function to_slug($str, $vietnamese = true, $special = true, $accent = true)
    {
        // Remove any '-' from the string since they will be used as concatenaters
        $str = str_replace('-', ' ', $str);
        // Remove Vietnamese accent or not
        $str = $accent ? self::remove_vietnamese_accent($str) : $str;
        // Replace special symbols with spaces or not
        $str = $special ? self::remove_special_characters($str) : $str;
        // Replace Vietnamese characters or not
        $str = $vietnamese ? self::replace_vietnamese_characters($str) : $str;
        // Trim white spaces at beginning and end of alias and make lowercase
        $str = trim(strtolower($str));
        // Remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);
        // Trim dashes at beginning and end of alias
        $str = trim($str, '-');

        return $str;
    }

    /**
     * Kiểm tra có phải là chuổi tiếng việt không dấu
     *
     * @param string $str
     * @return bool
     */
    public static function isNotSignVietnamese($str)
    {
        return !preg_match('/[^a-zA-Z0-9\-\.\/[:space:]]/', $str);
    }

    /**
     * Chuyển thành Tiếng Việt không dấu
     *
     * @param string $str
     * @param bool $lower
     * @return null|string
     */
    public static function stripVietnamese($str, $lower = true)
    {
        if (!$str) {
            return null;
        }
        $str = self::remove_vietnamese_accent($str);
        $str = self::replace_vietnamese_characters($str);

        return $lower ? strtolower($str) : $str;
    }

    /**
     * Remove 5 Vietnamese accent / tone marks if has Combining Unicode characters
     * Tone marks: Grave (`), Acute(´), Tilde (~), Hook Above (?), Dot Bellow(.)
     *
     * @param string $str
     * @return string
     */
    public static function remove_vietnamese_accent($str)
    {
        $str = preg_replace("/[\x{0300}\x{0301}\x{0303}\x{0309}\x{0323}]/u", "", $str);

        return $str;
    }

    /**
     * Remove or Replace special symbols with spaces
     *
     * @param string $str
     * @param bool $remove
     * @return string
     */
    public static function remove_special_characters($str, $remove = true)
    {
        // Remove or replace with spaces
        $substitute = $remove ? "" : " ";
        $str = preg_replace(
            "/[\x{0021}-\x{002D}\x{002F}\x{003A}-\x{0040}\x{005B}-\x{0060}\x{007B}-\x{007E}\x{00A1}-\x{00BF}]/u",
            $substitute,
            $str
        );

        return $str;
    }

    /**
     * Replace Vietnamese vowels with diacritic and Letter D with Stroke with corresponding English characters
     *
     * @param string $str
     * @return string
     */
    public static function replace_vietnamese_characters($str)
    {
        $str = preg_replace("/[\x{00C0}-\x{00C3}\x{00E0}-\x{00E3}\x{0102}\x{0103}\x{1EA0}-\x{1EB7}]/u", "a", $str);
        $str = preg_replace("/[\x{00C8}-\x{00CA}\x{00E8}-\x{00EA}\x{1EB8}-\x{1EC7}]/u", "e", $str);
        $str = preg_replace("/[\x{00CC}\x{00CD}\x{00EC}\x{00ED}\x{0128}\x{0129}\x{1EC8}-\x{1ECB}]/u", "i", $str);
        $str = preg_replace("/[\x{00D2}-\x{00D5}\x{00F2}-\x{00F5}\x{01A0}\x{01A1}\x{1ECC}-\x{1EE3}]/u", "o", $str);
        $str = preg_replace(
            "/[\x{00D9}-\x{00DA}\x{00F9}-\x{00FA}\x{0168}\x{0169}\x{01AF}\x{01B0}\x{1EE4}-\x{1EF1}]/u",
            "u",
            $str
        );
        $str = preg_replace("/[\x{00DD}\x{00FD}\x{1EF2}-\x{1EF9}]/u", "y", $str);
        $str = preg_replace("/[\x{0110}\x{0111}]/u", "d", $str);

        return $str;
    }
}
