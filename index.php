<?php
error_reporting(0);

require 'vk.php';
require 'post.php';

$token = 'YOUR TOKEN';
$user_id = null;
$group_id = null;

$text = 'YOUR TEXT';
$image = 'YOUR IMAGE';

try {
    $vk = \vkApi\vk::create($token);
    $post = new \vkApi\post($vk, $user_id, $group_id);
    $post->post($text, $image);
    echo 'Success!';
} catch(Exception $e){
    echo 'Error: <b>' . $e->getMessage() . '</b><br />';
    echo 'in file "' . $e->getFile() . '" on line ' . $e->getLine();
}