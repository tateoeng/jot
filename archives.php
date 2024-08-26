<?php
$this_page = "./archives.php";

include('initialize.php');

if (isset($_GET['submit'])) {
    $archive = $_GET['submit'];
    $body = file_get_contents($archive);
    $Parsedown = new Parsedown();
    include('head.php');
    print('<div id="masthead">');
    include('masthead.php');
print('    </div>');
    
    $archive_date = get_inline_timestamp($archive);
    
    print('<div id="feature">');
    print('    <h6>'.$archive.'</h6>');
    print('    <h4>'.$archive_date.'</h4>');
    print('        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
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
');
    print('    '.$Parsedown->text($body).'</div>');
    include('foot.php');
}




if (isset($_POST['submit']) && $_POST['submit'] == 'home') {
    header("Location: ./index.php");
}




else if (isset($_POST['submit']) && $_POST['submit'] == 'manage') {
    header("Location: ./manage.php");
}




else {
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('blogs'));
$archives = array(); 

/** @var SplFileInfo $file */
foreach ($rii as $archive) {
    if (!$archive->isDir() && str_ends_with($archive, '.md')) $archives[] = $archive->getPathname();        
}

include('head.php');
print('<div id="masthead">');
include('masthead.php');
print('</div>
    <div id="feature">
        <h2>Archives</h2>
        <form action="./archives.php" id="archives" method="get">');

foreach ($archives as $archive) {
    $file = fopen($archive, 'r');
    print('
            <button type="submit" id="view" name="submit" value="'.$archive.'">'.get_inline_timestamp($archive).': '.fgets($file).'</button>');
    fclose($file);
}

print('</form>');
print('</div>');

include('foot.php'); }
?>