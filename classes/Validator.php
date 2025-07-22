<?php
class Validator {
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    public static function notEmpty(...$fields) {
        foreach($fields as $f) if(trim($f) === "") return false;
        return true;
    }
    public static function minLength($str, $len) {
        return mb_strlen(trim($str)) >= $len;
    }
}
?>
