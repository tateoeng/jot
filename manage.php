<?php
$this_page = './manage.php';

include('initialize.php');




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





if (isset($_POST['submit']) && $_POST['submit'] == 'new_post') {
    include('head.php');
    print('<body>
    <div id="masthead">');
    include('masthead.php');    
    include('manage_menu.php');
    print('</div>
            
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
    
    include('head.php');
    print('<body>
        <div id="masthead">');
    include('masthead.php');
    include('manage_menu.php');    
    print('    </div>

        <div id="feature">
            <form action="'.$this_page.'" method="post">
                <label for="preferences">Preferences</label>
                <textarea id="preferences" name="preferences">'.$ini.'</textarea>
                <button type="submit" id="set_up" name="submit" value="set_up">Update</button></form></div></body></html>'); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'home') {
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "0"');
    fclose($file); 
    header("Location: ./index.php");
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'archives') {
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('blogs'));
$archives = array(); 

/** @var SplFileInfo $file */
foreach ($rii as $archive) {
    if (!$archive->isDir() && str_ends_with($archive, '.md')) $archives[] = $archive->getPathname();        
}

include('head.php');
print('<body>
    <div id="masthead">');
include('masthead.php');
include('manage_menu.php');
print('</div>

    <div id="feature">
        <h2>Archives</h2>');

foreach ($archives as $archive) {
    $Parsedown = new Parsedown();
    $file = fopen($archive, 'r');
    print('
        <form action="'.$this_page.'" method="post"><p>
            <button type="submit" class="view" name="submit" value="view">'.get_inline_timestamp($archive).$Parsedown->text(fgets($file)).'</button>
            <input type="hidden" class="archive" name="archive" value="'.$archive.'" /></p></form>');
    fclose($file);
}


print('</div>');

include('foot.php'); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'set_up') {
    $file = fopen('jot.ini', 'w');
    fwrite($file, $_POST['preferences']);
    fclose($file); 
    header("Refresh:0"); }





else if (isset($_POST['submit']) && $_POST['submit'] == 'stylesheet') {
    $style = file_get_contents('style.css');
include('head.php');
print('<body>
    <div id="masthead">');
include('masthead.php');
include('manage_menu.php');
print('    </div>

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
include('head.php');
print('<body>
    <div id="masthead">');
include('masthead.php');
include('manage_menu.php');
print('</div>');

    $archive_date = get_inline_timestamp($archive);
    
print('    <div id="feature">
        <h6>'.$archive.'</h6>
        <h4>'.$archive_date.'</h4>');
include('manage_post_menu.php');
print('        '.$Parsedown->text($body).'</div>');
print('</body></html>');
}





else if (isset($_POST['submit']) && $_POST['submit'] == 'adjust') {
    $archive = $_POST['archive'];
    $nts = $_POST['new_timestamp'];
    adjust_the_($archive, $nts);
    delete_the_($archive);
    header("Refresh:0");
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
include('head.php');
print('<body>
    <div id="masthead">');
include('masthead.php');
print('</div>');

    print('<div id="feature"><form action="'.$this_page.'" method="post">
<textarea name="body">'.$body.'</textarea>
<br />
<button type="submit" id="save" name="submit" value="save">Save</button>
<input type="hidden" id="archive" name="archive" value="'.$_POST['archive'].'">
</form></div></body></html>');
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





else if (isset($_POST['submit']) && $_POST['submit'] == 'previous') {
    $page = $page - 1;
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "'.$page.'"');
    fclose($file); 
    header("Location: ./manage.php");
}






else if (isset($_POST['submit']) && $_POST['submit'] == 'next') {
    $page = $page + 1;
    $file = fopen('page.ini', 'w');
    fwrite($file, 'page = "'.$page.'"');
    fclose($file); 
    header("Location: ./manage.php");
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
$printing_archives = array_slice($archives, $page * $blog_nposts, $blog_nposts);

include('head.php');
print('<body>
<div id="masthead">');
include('masthead.php');
include('manage_menu.php');
print('</div>');

foreach ($printing_archives as $archive) {
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    $archive_date = get_inline_timestamp($archive);
    
    print('
<div class="post">
    <h4>'.$archive_date.'</h4>');
    include('manage_post_menu.php');
            
print(''.$Parsedown->text($body).'</div>'); }
//print('<a href="#" class="top" onclick="scrollToTop();return false;">^</a>
//</body></html>'); }
include('foot.php'); }
?>
