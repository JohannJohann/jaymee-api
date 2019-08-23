<?php

$file = fopen('./abos.txt', 'r');
$content = stream_get_contents($file);
$arrayContent = array_unique(explode("\n", $content));
fclose($file);
echo count($arrayContent)

?>