<?php
print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title>
    <script>
        function scrollToTop(){
        var timerHandle = setInterval(function() {
            if (document.body.scrollTop != 0 || document.documentElement.scrollTop != 0)
                window.scrollBy(0,-50); else clearInterval(timerHandle); },10);
        }</script></head>

');
?>