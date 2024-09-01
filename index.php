<?php
$this_page = "./";

include('initialize.php');

if (isset($_POST['submit']) && $_POST['submit'] == 'archives') {
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


print('</div></div>');

}





else if (isset($_POST['submit']) && $_POST['submit'] == 'manage') {
    header("Location: ./manage.php");
}

else if (isset($_POST['submit']) && $_POST['submit'] == 'view') {
    $archive = $_POST['archive'];
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
include('head.php');
print('<body>
    <div id="masthead">');
include('masthead.php');
print('</div>');

    $archive_date = get_inline_timestamp($archive);
    
print('    <div id="feature">
        <h6>'.$archive.'</h6>
        <h4>'.$archive_date.'</h4>');
print('        '.$Parsedown->text($body).'</div>');
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
$printing_archives = array_slice($archives, 0, $ini['blog_nposts']);

include('head.php');
print('<div id="masthead">');
include('masthead.php');
print('</div>');

foreach ($printing_archives as $archive) {
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    $archive_date = get_inline_timestamp($archive);
    
    print('<div class="post">');
    print('    <h4>'.$archive_date.'</h4>');
    print($Parsedown->text($body));
    print('</div>');
}

}

include('foot.php');

?>
