<?php
$this_page = './manage.php';

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




function remove_tail($delimiter, $string) {
    $string = explode($delimiter, $string);
    array_pop($string);
    $string = implode($delimiter, $string);
    return $string;
}





function delete_the_($archive) {
    $archive = remove_tail('\\', $archive);
    $files = glob($archive.'\\*');
    foreach ($files as $file) { unlink($file); }
    $files = glob($archive.'\\*');
    $count = count(scandir($archive));
    while ($count == 2) {
        rmdir($archive);
        $archive = remove_tail('\\', $archive);
        $count = count(scandir($archive));
    }
}





function duplicate_the_($archive) {
    $old_directory = remove_tail('\\', $archive);
    $old_directory = $old_directory.'\\';
    $ini = parse_ini_file('jot.ini');
    $new_directory = date($ini['link_timestamp']);
    $new_directory = 'blogs/'.$new_directory.'/';
    $new_directory = str_replace('/', '\\', $new_directory);
    if (!is_dir($new_directory)) mkdir($new_directory, 0755, true);
    $files = glob($old_directory.'*');
    foreach ($files as $file) {
        if (!str_ends_with($file, '.md')) {
            $filename = explode('\\', $file);
            $filename = array_pop($filename);
            copy($file, $new_directory.$filename);
        } else {
            $filename = explode('\\', $file);
            $filename = array_pop($filename);
            $old_post = file_get_contents($file);
            $new_post = str_replace($old_directory, $new_directory, $old_post);
            $f = fopen($new_directory.$filename, 'w');
            fwrite($f, $new_post);
            fclose($f);
        }
    }
}





function adjust_the_($archive, $new_directory) {
    $old_directory = remove_tail('\\', $archive);
    $old_directory = $old_directory.'\\';
    $new_directory = str_replace('/', '\\', $new_directory);
    $new_directory = remove_tail('\\', $new_directory);
    $new_directory = $new_directory.'\\';
    if (!is_dir($new_directory)) mkdir($new_directory, 0755, true);
    $files = glob($old_directory.'*');
    foreach ($files as $file) {
        if (!str_ends_with($file, '.md')) {
            $filename = explode('\\', $file);
            $filename = array_pop($filename);
            copy($file, $new_directory.$filename);
        } else {
            $filename = explode('\\', $file);
            $filename = array_pop($filename);
            $old_post = file_get_contents($file);
            $new_post = str_replace($old_directory, $new_directory, $old_post);
            $f = fopen($new_directory.$filename, 'w');
            fwrite($f, $new_post);
            fclose($f);
        }
    }
}





date_default_timezone_set($blog_timezone);

// recursive md file search adapted from https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('blogs'));
$archives = array(); 

/** @var SplFileInfo $file */
foreach ($rii as $archive) {
    if (!$archive->isDir() && str_ends_with($archive, '.md')) $archives[] = $archive->getPathname();        
}

if ($ini['latest_first']) $archives = array_reverse($archives);
$printing_archives = array_slice($archives, $page * $blog_nposts, $blog_nposts);






if (!is_dir('blogs')) mkdir('blogs', 0755, true);
if (!is_dir('pages')) mkdir('pages', 0755, true);





if (isset($_POST['submit']) && $_POST['submit'] == 'home') {
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "0"');
    fclose($file); 
    header("Location: ./index.php");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'archives') {
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
    <h5>'.$blog_tagline.'</h5>
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" id="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></p></form></div>

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





else if (isset($_POST['submit']) && $_POST['submit'] == 'new_post') {
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
        <h5>'.$blog_tagline.'</h5>
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" id="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></p></form></div>
            
        <div id="feature"><form action="'.$this_page.'" method="post">
            <label for="New post">New post</label>
            <textarea name="body"></textarea>
            <button type="submit" id="publish" name="submit" value="publish">Publish</button></form></div></body></html>'); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'publish') {
    $new_timestamp = date($ini['link_timestamp']);
    $new_timestamp = 'blogs/'.$new_timestamp;
    if (!is_dir($new_timestamp)) mkdir($new_timestamp, 0755, true);
    if (!file_exists($new_timestamp.'index.md')) {
        $file = fopen($new_timestamp.'/index.md', 'w');
        fwrite($file, $_POST['body']);
        fclose($file); 
    } 
    header("Refresh:0");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'preferences') {
    $ini = file_get_contents('jot.ini');
    
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
    <h5>'.$blog_tagline.'</h5>
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" id="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></p></form></div>

        <div id="feature">
            <form action="'.$this_page.'" method="post">
                <label for="preferences">Preferences</label>
                <textarea id="preferences" name="preferences">'.$ini.'</textarea>
                <button type="submit" id="set_up" name="submit" value="set_up">Update</button></form></div></body></html>'); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'set_up') {
    $file = fopen('jot.ini', 'w');
    fwrite($file, $_POST['preferences']);
    fclose($file); 
    header("Refresh:0"); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'stylesheet') {
    $style = file_get_contents('style.css');
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
    <h5>'.$blog_tagline.'</h5>
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" id="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></p></form>
    </div>

    <div id="feature">
        <form action="'.$this_page.'" method="post">
            <label for="stylesheet">Stylesheet</label>
            <textarea name="body">'.$style.'</textarea><br />
            <button type="submit" id="save_stylesheet" name="submit" value="save_stylesheet">Update</button></form></div></body></html>'); }






else if (isset($_POST['submit']) && $_POST['submit'] == 'save_stylesheet') {
    $file = fopen('style.css', 'w');
    fwrite($file, $_POST['body']);
    fclose($file); 
    header("Refresh:0"); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'view') {
    $archive = $_POST['archive'];
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    $archive_date = get_inline_timestamp($archive);
    
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
    <h5>'.$blog_tagline.'</h5>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" id="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></p></form></div>

    <div id="feature">
        <h6>'.$archive.'</h6>
        <h4>'.$archive_date.'</h4>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <p>
                <button type="submit" class="view" name="submit" value="view">View</button>
                <button type="submit" class="edit" name="submit" value="edit">Edit</button>
                <input type="file" class="file_to_upload" name="file_to_upload" />
                <input type="submit" class="upload" name="submit" value="Upload" />
                <br />
                <button type="submit" class="duplicate" name="submit" value="duplicate">Duplicate</button>
                <button type="submit" class="restamp" name="submit" value="restamp">Restamp</button>
                <input type="text" class="new_timestamp" name="new_timestamp" value="'.$archive.'" />
                <button type="submit" class="adjust" name="submit" value="adjust">Adjust</button>
                <button type="submit" class="delete" name="submit" value="delete">Delete</button>
                <input type="hidden" class="archive" name="archive" value="'.$archive.'" /></p></form>
                
        <br /><br />
        '.$Parsedown->text($body).'</div></body></html>');
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'edit') {
    $body = file_get_contents($_POST['archive']);
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
        
    <div id="feature"><form action="'.$this_page.'" method="post">
        <textarea name="body">'.$body.'</textarea>
        <br />
        <button type="submit" id="save" name="submit" value="save">Save</button>
        <input type="hidden" id="archive" name="archive" value="'.$_POST['archive'].'"></form></div></body></html>');
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'save') {
    $file = fopen($_POST['archive'], 'w');
    fwrite($file, $_POST['body']);
    fclose($file);
    header("Refresh:0");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'Upload') {
    $target_dir = $_POST['archive'];
    $target_dir = explode('\\', $target_dir);
    array_pop($target_dir);
    $target_dir = implode('\\', $target_dir);
    $target_file = $target_dir.'\\'.basename($_FILES["file_to_upload"]["name"]);
    move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_file);
    header("Refresh:0");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'duplicate') {
    // get link from previous entry
    $archive = $_POST['archive'];
    duplicate_the_($archive);
    header("Refresh:0");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'restamp') {
    $archive = $_POST['archive'];
    duplicate_the_($archive);
    delete_the_($archive);
    header("Refresh:0");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'adjust') {
    $archive = $_POST['archive'];
    $nts = $_POST['new_timestamp'];
    adjust_the_($archive, $nts);
    delete_the_($archive);
    header("Refresh:0");
}




else if (isset($_POST['submit']) && $_POST['submit'] == 'delete') {
    $archive = $_POST['archive'];
    delete_the_($archive);
    header("Refresh:0");
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
        <h5>'.$blog_tagline.'</h5>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <p>
                <button type="submit" id="new_post" name="submit" value="new_post">New post...</button>
                <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
                <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></p></form></div>');

foreach ($printing_archives as $archive) {
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    $archive_date = get_inline_timestamp($archive);
    
    print('

    <div class="post">
        <h4>'.$archive_date.'</h4>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <p>
                <button type="submit" class="view" name="submit" value="view">View</button>
                <button type="submit" class="edit" name="submit" value="edit">Edit</button>
                <input type="file" class="file_to_upload" name="file_to_upload" />
                <input type="submit" class="upload" name="submit" value="Upload" />
                <br />
                <button type="submit" class="duplicate" name="submit" value="duplicate">Duplicate</button>
                <button type="submit" class="restamp" name="submit" value="restamp">Restamp</button>
                <input type="text" class="new_timestamp" name="new_timestamp" value="'.$archive.'" />
                <button type="submit" class="adjust" name="submit" value="adjust">Adjust</button>
                <button type="submit" class="delete" name="submit" value="delete">Delete</button>
                <input type="hidden" class="archive" name="archive" value="'.$archive.'" /></p></form>

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
