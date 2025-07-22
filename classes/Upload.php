<?php
class Upload {
    public static function image($file, $folder = "uploads/") {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if(!in_array($ext, $allowed)) return false;
        $name = uniqid("img_") . "." . $ext;
        $target = $folder . $name;
        if(move_uploaded_file($file['tmp_name'], $target)) return $target;
        return false;
    }
}
?>
