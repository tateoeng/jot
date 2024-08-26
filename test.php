<?php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

echo $converter->convert('# Hello World!');
?>