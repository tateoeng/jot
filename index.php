<?php
$this_page = './index.php';

include('Parsedown.php');
$ini = parse_ini_file('jot.ini');
$blog_name = $ini['blog_name'];
$blog_tagline = $ini['blog_tagline'];
$blog_timezone = $ini['blog_timezone'];
$link_timestamp = $ini['link_timestamp'];
$blog_nposts = $ini['blog_nposts'];
$latest_first = $ini['latest_first'];

$page = parse_ini_file('page.ini');
$page = $page['page'];

function get_inline_timestamp($ts) {
    $ts = explode('\\', $ts);
    array_shift($ts);
    array_pop($ts);
    
    $count = count($ts);
    if ($count == 1) {
        return date('Y', mktime(0, 0, 0, 0, 0, $ts[0]));
    } elseif ($count == 2) {
        return date('F Y', mktime(0, 0, 0, $ts[1], 0, $ts[0]));
    } elseif ($count == 3) {
        return date('F j, Y', mktime(0, 0, 0, $ts[1], $ts[2], $ts[0]));
    } elseif ($count == 4) {
        return date('F j, Y, H \o\'\c\l\o\c\k', mktime($ts[3], 0, 0, $ts[1], $ts[2], $ts[0]));
    } elseif ($count == 5) {
        return date('F j, Y, H:i', mktime($ts[3], $ts[4], 0, $ts[1], $ts[2], $ts[0]));
    } elseif ($count == 6) {
        return date('F j, Y, H:i:s', mktime($ts[3], $ts[4], $ts[5], $ts[1], $ts[2], $ts[0])); } }




date_default_timezone_set($blog_timezone);

if (!is_dir('blogs')) mkdir('blogs', 0755, true);
if (!is_dir('pages')) mkdir('pages', 0755, true);





// recursive md file search adapted from 
// https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('blogs'));
$archives = array(); 
/** @var SplFileInfo $file */
foreach ($rii as $archive) {
    if (!$archive->isDir() && str_ends_with($archive, '.md')) $archives[] = $archive->getPathname();        
}

if ($ini['latest_first']) $archives = array_reverse($archives);
$printing_archives = array_slice($archives, $page * $blog_nposts, $blog_nposts);






if (isset($_POST['submit']) && $_POST['submit'] == 'archives') {
print('<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title>
    <script type="text/javascript">
        function scrollToTop(){
        var timerHandle = setInterval(function() {
            if (document.body.scrollTop != 0 || document.documentElement.scrollTop != 0)
                window.scrollBy(0,-50); else clearInterval(timerHandle); },10);
        }</script></head>

<body>
    <div id="masthead">
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" class="home" name="submit" value="home">Home</button>
            <button type="submit" class="archives" name="submit" value="archives">Archives</button>
            <button type="submit" class="manage" name="submit" value="manage">Manage</button></p></form>
    <h1><a href="./">'.$blog_name.'</a></h1>
    <h5>'.$blog_tagline.'</h5></div>

    <div id="feature">
        <h2>Archives</h2>
        <div id="archives">');

foreach ($archives as $archive) {
    $Parsedown = new Parsedown();
    $file = fopen($archive, 'r');
    print('
            <form action="'.$this_page.'" method="post">
                <p style="display: inline;">
                    <button type="submit" class="view" name="submit" value="view">'.get_inline_timestamp($archive).$Parsedown->text(fgets($file)).'</button>
                    <input type="hidden" class="archive" name="archive" value="'.$archive.'" /></p></form>');
    fclose($file);
}


print('</div></div>
<div id="footer">&nbsp;
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" class="home" name="submit" value="home">Home</button>
            <button type="submit" class="archives" name="submit" value="archives">Archives</button>
            <button type="submit" class="manage" name="submit" value="manage">Manage</button></p></form>
    ');
if (isset($_POST['submit']) && $_POST['submit'] == 'archives') {
    print('<p style="text-align: center;">Entries '.count($archives).' of '.count($archives).'</p>');
} else {
    if ($blog_nposts != 1) print('<p style="text-align: center;">Entries '.$page * $blog_nposts + 1 .' - '.$page * $blog_nposts + $blog_nposts.' of '.count($archives).'</p>
    ');
    else print('<p style="text-align: center">Entry '.$page * $blog_nposts + 1 .' of '.count($archives).'</p>
    ');
    if ($blog_nposts >= count($archives)) print('<p style="text-align: center">Page 1 of 1
    ');
    else print('<p style="text-align: center">Page '.$page + 1 .' of '.ceil(count($archives) / $blog_nposts).'</p>
    ');
    print('<form action="'.$this_page.'" method="post" enctype="mutlipart/form-data">
        <p>');
    if ($page != 0) print('<button type="submit" id="previous" name="submit" value="previous">Previous '.$blog_nposts.' entries</button>');
    if ($page + 1 != ceil(count($archives) / $blog_nposts)) print('
<button type="submit" id="next" name="submit" value="next">Next '.$blog_nposts.' entries</button>');}
    print('</p></form><br /><br /></div>
<a href="#" id="top" onclick="scrollToTop();return false;">^</a>
</body></html>'); 
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'manage') {
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "0"');
    // fclose.($file); 
    header("Location: ./manage.php");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'beginning') {
    $page = 0;
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "'.$page.'"');
    fclose($file); 
    header("Location: ./".$this_page);
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'previous') {
    $page = $page - 1;
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "'.$page.'"');
    fclose($file); 
    header("Location: ./".$this_page);
}






else if (isset($_POST['submit']) && $_POST['submit'] == 'next') {
    $page = $page + 1;
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "'.$page.'"');
    fclose($file); 
    header("Location: ./".$this_page);
}






else if (isset($_POST['submit']) && $_POST['submit'] == 'end') {
    $page = ceil(count($archives) / $blog_nposts) - 1;
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "'.$page.'"');
    fclose($file); 
    header("Location: ./".$this_page);
}





else {
print('<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title>
    <script type="text/javascript">
        function scrollToTop(){
        var timerHandle = setInterval(function() {
            if (document.body.scrollTop != 0 || document.documentElement.scrollTop != 0)
                window.scrollBy(0,-50); else clearInterval(timerHandle); },10);
        }</script></head>

<body>
    <div id="masthead">
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <p>
                <button type="submit" class="home" name="submit" value="home">Home</button>
                <button type="submit" class="archives" name="submit" value="archives">Archives</button>
                <button type="submit" class="manage" name="submit" value="manage">Manage</button></p></form>

        <h1><a href="./">'.$blog_name.'</a></h1>
        <h5>'.$blog_tagline.'</h5></div>');

foreach ($printing_archives as $archive) {
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    $archive_date = get_inline_timestamp($archive);
    
    print('

    <div class="post">
        <h4>'.$archive_date.'</h4>
        <br /><br />
        
        
        '.$Parsedown->text($body).'</div>'); }
print('


    <div id="footer">&nbsp;
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <p>
                <button type="submit" class="home" name="submit" value="home">Home</button>
                <button type="submit" class="archives" name="submit" value="archives">Archives</button>
                <button type="submit" class="manage" name="submit" value="manage">Manage</button></p></form>
                ');
if (isset($_POST['submit']) && $_POST['submit'] == 'archives') {
    print('
    <p style="text-align: center;">Entries '.count($archives).' of '.count($archives).'</p>');
} else {
    if ($blog_nposts != 1) print('
        <p style="text-align: center;">Entries '.$page * $blog_nposts + 1 .' - '.$page * $blog_nposts + $blog_nposts.' of '.count($archives).'</p>
    ');
    else print('        <p style="text-align: center">Entry '.$page * $blog_nposts + 1 .' of '.count($archives).'</p>
    ');


    
    if ($blog_nposts >= count($archives)) print('        <p style="text-align: center">Page 1 of 1
    ');
    else print('        <p style="text-align: center">Page '.$page + 1 .' of '.ceil(count($archives) / $blog_nposts).'</p>
    ');



    print('        <form action="'.$this_page.'" method="post" enctype="mutlipart/form-data">
            <p>');
    print('
                <button type="submit" id="beginning" name="submit" value="beginning">&lt;&lt; Beginning</button>');
    if ($page != 0) print('
                <button type="submit" id="previous" name="submit" value="previous">&lt; Previous '.$blog_nposts.' entries</button>');
    if ($page + 1 != ceil(count($archives) / $blog_nposts)) print('
                <button type="submit" id="next" name="submit" value="next">Next '.$blog_nposts.' entries &gt;</button>');}
    print('
                <button type="submit" id="end" name="submit" value="end">End &gt;&gt;</button>');     
    print('</p></form><br /><br /></div>

    <a href="#" id="top" onclick="scrollToTop();return false;">^</a></body></html>'); }
?>
