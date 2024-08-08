<?php
include('Parsedown.php');
$ini = parse_ini_file('jot.ini');
$blog_name = $ini['blog_name'];
$blog_tagline = $ini['blog_tagline'];
$blog_timezone = $ini['blog_timezone'];
$link_timestamp = $ini['link_timestamp'];
$blog_nposts = $ini['blog_nposts'];
$latest_first = $ini['latest_first'];

$this_page = "./";

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




if (isset($_POST['submit']) && $_POST['submit'] == 'manage') {
    header("Location: ./manage.php");
}

if (isset($_POST['submit']) && $_POST['submit'] == 'archives') {
    header("Location: ./archives.php");
}

// recursive md file search adapted from https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('blogs'));
$archives = array(); 

/** @var SplFileInfo $file */
foreach ($rii as $archive) {
    if (!$archive->isDir() && str_ends_with($archive, '.md')) $archives[] = $archive->getPathname();        
}

if ($ini['latest_first']) $archives = array_reverse($archives);
$archives = array_slice($archives, 0, $ini['blog_nposts']);

include('head.php');
print('<div id="masthead">');
include('masthead.php');
print('</div>');

foreach ($archives as $archive) {
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    $archive_date = get_inline_timestamp($archive);
    
    print('<div class="post">');
    print('    <h4>'.$archive_date.'</h4>');
    print($Parsedown->text($body));
    print('</div>');
}

include('foot.php');

?>
