<?php

$app_id = "<INSERT APP ID HERE>";
$base_url = "https://graph.facebook.com/v2.7/";


// Returns filesystem-safe string after cleaning, filtering, and trimming input
function str_file_filter(
    $str,
    $sep = '_',
    $strict = false,
    $trim = 248) {

    $str = strip_tags(htmlspecialchars_decode(strtolower($str))); // lowercase -> decode -> strip tags
    $str = str_replace("%20", ' ', $str); // convert rogue %20s into spaces
    $str = preg_replace("/%[a-z0-9]{1,2}/i", '', $str); // remove hexy things
    $str = str_replace("&nbsp;", ' ', $str); // convert all nbsp into space
    $str = preg_replace("/&#?[a-z0-9]{2,8};/i", '', $str); // remove the other non-tag things
    $str = preg_replace("/\s+/", $sep, $str); // filter multiple spaces
    $str = preg_replace("/\.+/", '.', $str); // filter multiple periods
    $str = preg_replace("/^\.+/", '', $str); // trim leading period

    if ($strict) {
        $str = preg_replace("/([^\w\d\\" . $sep . ".])/", '', $str); // only allow words and digits
    } else {
        $str = preg_replace("/([^\w\d\\" . $sep . "\[\]\(\).])/", '', $str); // allow words, digits, [], and ()
    }

    $str = preg_replace("/\\" . $sep . "+/", $sep, $str); // filter multiple separators
    $str = substr($str, 0, $trim); // trim filename to desired length, note 255 char limit on windows

    return $str;
}


// Returns full file name including fallback and extension
function str_file(
    $str,
    $sep = '_',
    $ext = '',
    $default = '',
    $trim = 248) {

    // Run $str and/or $ext through filters to clean up strings
    $str = str_file_filter($str, $sep);
    $ext = '.' . str_file_filter($ext, '', true);

    // Default file name in case all chars are trimmed from $str, then ensure there is an id at tail
    if (empty($str) && empty($default)) {
        $str = 'no_name__' . date('Y-m-d_H-m_A') . '__' . uniqid();
    } elseif (empty($str)) {
        $str = $default;
    }

    // Return completed string
    if (!empty($ext)) {
        return $str . $ext;
    } else {
        return $str;
    }
}
?>