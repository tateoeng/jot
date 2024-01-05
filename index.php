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
        return date('F j, Y, H\o\'\c\l\o\c\k', mktime($ts[3], 0, 0, $ts[1], $ts[2], $ts[0]));
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





function remove_head($delimiter, $string) {
    $string = explode($delimiter, $string);
    array_shift($string);
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

if (!is_dir('blogs')) mkdir('blogs', 0755, true);
if (!is_dir('pages')) mkdir('pages', 0755, true);





if (isset($_GET['submit'])) {
    $archive = $_GET['submit'];
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    include('head.php');
print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title></head>

<body>
    <div id="masthead">
    <h1><a href="'.$this_page.'">'.$blog_name.'</a></h1>
    <h6>'.$blog_tagline.'</h6>
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <button type="submit" class="new_post" name="submit" value="new_post">New post...</button>
        <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
        <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button>
        <br /><br />
        <button type="submit" id="archives" name="submit" value="archives">Archives</button></form></div>');
        
    
    $archive_date = get_inline_timestamp($archive);
    
    print('<div id="feature">
    <h6>'.$archive.'</h6>
    <h4>'.$archive_date.'</h4>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <button type="submit" id="delete" name="submit" value="delete">Delete</button>
            <button type="submit" id="duplicate" name="submit" value="duplicate">Duplicate</button>
            <button type="submit" id="restamp" name="submit" value="restamp">Restamp</button>
            <input type="text" id="new_timestamp" name="new_timestamp" value="'.$archive.'" />
            <button type="submit" id="adjust" name="submit" value="adjust">Adjust</button>
            <br />
            <button type="submit" id="view" name="submit" value="view">View</button>
            <button type="submit" id="edit" name="submit" value="edit">Edit</button>
            <input type="file" class="file_to_upload" name="file_to_upload" />
            <input type="submit" class="upload" name="submit" value="Upload" />
            <input type="hidden" id="body" name="body" value="'.$body.'" />
            <input type="hidden" id="archive" name="archive" value="'.$archive.'" /></form>');
    print('    '.$Parsedown->text($body).'</div>');
    include('foot.php');
}


else if (isset($_POST['submit']) && $_POST['submit'] == 'new_post') {
    
print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title></head>

<body>
    <div id="masthead">
    <h1><a href="'.$this_page.'">'.$blog_name.'</a></h1>
    <h6>'.$blog_tagline.'</h6>
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <button type="submit" class="new_post" name="submit" value="new_post">New post...</button>
        <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
        <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></form></div>
        
    <div id="feature"><form action="'.$this_page.'" method="post">
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
    
print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title></head>

<body>
    <div id="masthead">
        <h1><a href="'.$this_page.'">'.$blog_name.'</a></h1>
        <h6>'.$blog_tagline.'</h6>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <button type="submit" class="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></form></div>

    <div id="feature">
        <form action="'.$this_page.'" method="post">
            <label for="preferences">Preferences</label>
            <textarea id="preferences" name="preferences">'.$ini.'</textarea>
            <button type="submit" id="set_up" name="submit" value="set_up">Update</button></form></div></body></html>'); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'archives') {
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('blogs'));
$archives = array(); 

/** @var SplFileInfo $file */
foreach ($rii as $archive) {
    if (!$archive->isDir() && str_ends_with($archive, '.md')) $archives[] = $archive->getPathname();        
}

print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title></head>

<body>
    <div id="masthead">
    <h1><a href="'.$this_page.'">'.$blog_name.'</a></h1>
    <h6>'.$blog_tagline.'</h6>
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <button type="submit" class="new_post" name="submit" value="new_post">New post...</button>
        <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
        <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button>
        <br /><br />
        <button type="submit" id="archives" name="submit" value="archives">Archives</button></form></div>

    <div id="feature">
        <h2>Archives</h2>
        <form action="./manage.php" method="get">');

foreach ($archives as $archive) {
    $file = fopen($archive, 'r');
    print('
            <button type="submit" id="view" name="submit" value="'.$archive.'">'.get_inline_timestamp($archive).': '.fgets($file).'</button>');
    fclose($file);
}

print('</form>');
print('</div>');

include('foot.php'); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'set_up') {
    $file = fopen('jot.ini', 'w');
    fwrite($file, $_POST['preferences']);
    fclose($file); 
    header("Refresh:0"); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'stylesheet') {
    $style = file_get_contents('style.css');
print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title></head>

<body>
    <div id="masthead">
        <h1><a href="'.$this_page.'">'.$blog_name.'</a></h1>
        <h6>'.$blog_tagline.'</h6>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <button type="submit" class="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></form></div>

    <div id="feature">
        <form action="'.$this_page.'" method="post">
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
print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title></head>

<body>
    <div id="masthead">
        <h1><a href="'.$this_page.'">'.$blog_name.'</a></h1>
        <h6>'.$blog_tagline.'</h6>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <button type="submit" class="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></form></div>');

    $archive_date = get_inline_timestamp($archive);
    
print('    <div id="feature">
        <h6>'.$archive.'</h6>
        <h4>'.$archive_date.'</h4>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <button type="submit" id="view" name="submit" value="view">View</button>
            <button type="submit" id="duplicate" name="submit" value="duplicate">Duplicate</button>
            <button type="submit" id="delete" name="submit" value="delete">Delete</button>
            <button type="submit" id="restamp" name="submit" value="restamp">Restamp</button>
            <button type="submit" id="edit" name="submit" value="edit">Edit</button>
            <input type="file" class="file_to_upload" name="file_to_upload" />
            <input type="submit" class="upload" name="submit" value="Upload" />
            <input type="hidden" id="body" name="body" value="'.$body.'" />
            <input type="hidden" id="archive" name="archive" value="'.$archive.'" /></form>
        '.$Parsedown->text($body).'</div></body></html>');
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'adjust') {
    $archive = $_POST['archive'];
    $nts = $_POST['new_timestamp'];
    adjust_the_($archive, $nts);
    delete_the_($archive);
    
}




else if (isset($_POST['submit']) && $_POST['submit'] == 'duplicate') {
    // get link from previous entry
    $archive = $_POST['archive'];
    duplicate_the_($archive);
    header("Refresh:0");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'delete') {
    $archive = $_POST['archive'];
    delete_the_($archive);
    header("Refresh:0");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'restamp') {
    $archive = $_POST['archive'];
    duplicate_the_($archive);
    delete_the_($archive);
    header("Refresh:0");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'edit') {
    $body = file_get_contents($_POST['archive']);
print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title></head>

<body>
    <div id="masthead">
        <h1><a href="'.$this_page.'">'.$blog_name.'</a></h1>
        <h6>'.$blog_tagline.'</h6>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <button type="submit" class="new_post" name="submit" value="new_post">New post...</button>
            <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
            <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button></form></div>');

    print('<div id="feature"><form action="'.$this_page.'" method="post">
<textarea name="body">'.$body.'</textarea>
<br />
<button type="submit" id="save" name="submit" value="save">Save</button>
<input type="hidden" id="archive" name="archive" value="'.$_POST['archive'].'" />
</form></div>
');

print('</body></html>');
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





else {
// recursive md file search adapted from https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('blogs'));
$archives = array(); 

/** @var SplFileInfo $file */
foreach ($rii as $archive) {
    if (!$archive->isDir() && str_ends_with($archive, '.md')) $archives[] = $archive->getPathname();        
}

if ($ini['latest_first']) $archives = array_reverse($archives);
$archives = array_slice($archives, 0, $ini['blog_nposts']);

print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>'.$blog_name.': '.$blog_tagline.'</title></head>

<body>
    <div id="masthead">
    <h1><a href="'.$this_page.'">'.$blog_name.'</a></h1>
    <h6>'.$blog_tagline.'</h6>
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <button type="submit" class="new_post" name="submit" value="new_post">New post...</button>
        <button type="submit" id="preferences" name="submit" value="preferences">Preferences...</button>
        <button type="submit" id="stylesheet" name="submit" value="stylesheet">Edit stylesheet...</button>
        <br /><br />
        <button type="submit" id="archives" name="submit" value="archives">Archives</button></form></div>');

foreach ($archives as $archive) {
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    $archive_date = get_inline_timestamp($archive);
    
    print('    <div class="post">
        <h4>'.$archive_date.'</h4>
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
            <button type="submit" id="delete" name="submit" value="delete">Delete</button>
            <button type="submit" id="duplicate" name="submit" value="duplicate">Duplicate</button>
            <button type="submit" id="restamp" name="submit" value="restamp">Restamp</button>
            <input type="text" id="new_timestamp" name="new_timestamp" value="'.$archive.'" />
            <button type="submit" id="adjust" name="submit" value="adjust">Adjust</button>
            <br />
            <button type="submit" id="view" name="submit" value="view">View</button>
            <button type="submit" id="edit" name="submit" value="edit">Edit</button>
            <input type="file" class="file_to_upload" name="file_to_upload" />
            <input type="submit" class="upload" name="submit" value="Upload" />
            <input type="hidden" id="body" name="body" value="'.$body.'" />
            <input type="hidden" id="archive" name="archive" value="'.$archive.'" /></form>
            
'.$Parsedown->text($body).'</div>

</body></html>'); } }
?>
