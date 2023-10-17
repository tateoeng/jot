<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<?php
include('Parsedown.php');

// if (isset($_GET['submit'])) {
    // print('<a href="./">$_GET[\'submit\'] = '.$_GET['submit'].'</a>');
    // print('<hr />');
// }

if (isset($_GET['blog_timezone'])) {
    date_default_timezone_set($_GET['blog_timezone']);
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Set_up') {
    if (!file_exists('jot.ini')) {
        $file = fopen('jot.ini', 'w');
        // fwrite($file, 'Jot_loaded = "Jot.ini loaded"'.PHP_EOL);
        // fwrite($file, 'blog_number_posts = "3"'.PHP_EOL);
        fwrite($file, 'blog_name = "'.$_GET['blog_name'].'"'.PHP_EOL);
        fwrite($file, 'blog_tagline = "'.$_GET['blog_tagline'].'"'.PHP_EOL);
        fwrite($file, 'blog_timezone = "'.$_GET['blog_timezone'].'"'.PHP_EOL);
        fwrite($file, 'blog_timestamp = "'.$_GET['blog_timestamp'].'"'.PHP_EOL);
        fwrite($file, 'link_timestamp = "'.$_GET['link_timestamp'].'"'.PHP_EOL);
        fwrite($file, 'blog_nposts= "'.$_GET['blog_nposts'].'"'.PHP_EOL);
        fclose($file); 
    } else {
        $file = fopen('jot.ini', 'w');
        fwrite($file, $_GET['body'].PHP_EOL);
        fclose($file);
    }
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Save_stylesheet') {
    $file = fopen('style.css', 'w');
    fwrite($file, $_GET['body'].PHP_EOL);
    fclose($file);
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Preferences') {
    $ini = file_get_contents('jot.ini');
    echo '<div class="feature">
<form action="./" method="get">
<textarea name="body">'.$ini.'</textarea>
<p>&nbsp;</p>
<button type="submit" id="Set_up" name="submit" value="Set_up">Update</button>
</form>
</div>';
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Stylesheet') {
    $style = file_get_contents('style.css');
    echo '<div class="feature">
<form action="./" method="get">
<textarea name="body">'.$style.'</textarea>
<br />
<button type="submit" id="Save_stylesheet" name="submit" value="Save_stylesheet">Update</button>
</form>
</div>';
}





if (isset($_GET['submit']) && $_GET['submit'] == 'New_post') {
    echo '<form class="feature" action="./" method="get">
<p><textarea name="body"></textarea></p>
<p><button type="submit" id="Publish" name="submit" value="Publish">Publish</button></p>
</form>';
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Publish') {
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
    $new_timestamp = date($ini['link_timestamp']);
    if (!is_dir($new_timestamp)) mkdir($new_timestamp, 0755, true);
    if (!file_exists($new_timestamp.'index.md')) {
        $file = fopen($new_timestamp.'/index.md', 'w');
        fwrite($file, $_GET['body'].PHP_EOL);
        fclose($file); 
    } 
}





if (isset($_GET['submit']) && $_GET['submit'] == 'View') {
    $post = file_get_contents($_GET['link'].'index.md');
    $Parsedown = new Parsedown();
    print('<div class="feature">');
    echo $Parsedown->text($post);
    print($_GET['link']);
    echo '<form action="./" method="get">
<button type="submit" id="Edit" name="submit" value="Edit">Edit</button>
<button type="submit" id="Duplicate" name="submit" value="Duplicate">Duplicate</button>
<button type="submit" id="Delete" name="submit" value="Delete">Delete</button>
<button type="submit" id="Restamp" name="submit" value="Restamp">Restamp</button>
<input type="file" class="File_to_upload" name="the_file" />
<input type="submit" class="Upload" name="submit" value="Upload" />
<input type="hidden" id="Link" name="link" value="'.$_GET['link'].'" />
</form>';
    print('</div>'.PHP_EOL);
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Edit') {
    $post = file_get_contents($_GET['link'].'index.md');
    echo '<form action="./" method="get">
<textarea name="body">'.$post.'</textarea>
<br />
<button type="submit" id="Save" name="submit" value="Save">Save</button>
<input type="hidden" id="Link" name="link" value="'.$_GET['link'].'" />
</form>
<hr />';
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Save') {
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
    $file = fopen($_GET['link'].'index.md', 'w');
    fwrite($file, $_GET['body'].PHP_EOL);
    fclose($file);
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Duplicate') {
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
    $new_timestamp = date($ini['link_timestamp']);
    if (!is_dir($new_timestamp)) mkdir($new_timestamp, 0755, true);
    $file = fopen($new_timestamp.'/index.md', 'w');
    $post = file_get_contents($_GET['link'].'index.md');
    fwrite($file, $post.PHP_EOL);
    fclose($file); 
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Delete') {
    $files = glob($_GET['link'].'*');
    foreach ($files as $file) {
        unlink($file);
    }
    rmdir($_GET['link']);
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Restamp') {
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
    $new_timestamp = date($ini['link_timestamp']);
    if (!is_dir($new_timestamp)) mkdir($new_timestamp, 0755, true);
    $file = fopen($new_timestamp.'/index.md', 'w');
    $post = file_get_contents($_GET['link'].'index.md');
    fwrite($file, $post.PHP_EOL);
    fclose($file); 

    $files = glob($_GET['link'].'*');
    foreach ($files as $file) {
        print($file.'<br>');
        unlink($file);
    }
    rmdir($_GET['link']);
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Upload') {
    // adapted from https://www.w3schools.com/php/php_file_upload.asp
    print($_GET['link']);
    $target_dir = $_GET['link'];
    $target_file = $target_dir . basename($_FILES["File_to_upload"]["name"]);
    move_uploaded_file($_FILES["File_to_upload"]["tmp_name"], $target_file);
}





if (isset($_GET['submit']) && $_GET['submit'] == 'Show_archives') {
    // etc.
}





// if the ini file does not exist, show init form.
if (!file_exists('jot.ini')) {
    echo '<h1>welcome to jot</h1>
<p id="problem_p">please fill out the form below to configure your blog</p>
<form action="./" method="get">
<h3><b>what</b></h3>
<p>blog name</p>
<p><input type="text" name="blog_name" /></p>
<p>tagline</p>
<p><input type="text" name="blog_tagline" /></p>
<p>timezone</p>
<p>(Timezone will take the form of Country/City. Example: America/Chicago. See <a href="https://en.wikipedia.org/wiki/List_of_tz_database_time_zones">here</a> for complete list of options.)</p>
<p><input type="text" name="blog_timezone" /></p>

<h3><b>how</b></h3>
<p>inline timestamp format (see <a href="http://man7.org/linux/man-pages/man1/data.1.html">here</a> for format examples)</p>
<p><input type="text" name="blog_timestamp" /></p>
<p>link timestamp format</p><p>(This will define the link structure where archive posts and their assets will be saved. Use PHP-style date format codes. Example: <code>Y/m/d-h.i</code> produces something like <code>https://dummydomain.com/2023/10/14-08.40</code>)</p>
<p><input type="text" name="link_timestamp" /></p>
<p>number of posts to display on the main page</p>
<p><input type="text" name="blog_nposts" /></p>
<button type="submit" id="Set_up" name="submit" value="Set_up">Set up</button>
</form>';





// if the ini file exists,
} else {
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
	$wildpath = explode('/', $ini['link_timestamp']);
	$count = count($wildpath);
	$wildpath = implode('/', array_fill(0, $count, '*'));
    $archives = glob($wildpath.'/index.md');
	// if there are no archives, show text box with publish button.
    if (count($archives) == 0) {
        echo '<form action="./" method="get">
<p><textarea name="body"></textarea></p>
<p><button type="submit" id="Publish" name="submit" value="Publish">Publish</button></p>
</form>';


	// if archives exist, show the blog.
    } else {
		echo '<div class="masthead"><div class="blog_name"><h1><a href="./">'.$ini['blog_name'].'</a></h1><h6 class="tagline">'.
$ini['blog_tagline'].'</h6></div>
<form action="./" method="get">
<button type="submit" class="New_s" name="submit" value="New_post">New post...</button>
<button type="submit" id="Preferences" name="submit" value="Preferences">Preferences...</button>
<button type="submit" id="Stylesheet" name="submit" value="Stylesheet">Edit stylesheet...</button>
<input type="hidden" id="Body" name="body" value="Body" />
</form></div>';
        $wildpath = explode('/', $ini['link_timestamp']);
		$count = count($wildpath);
		$wildpath = implode('/', array_fill(0, $count, '*'));
		$archives = glob($wildpath.'/'); // need function from Y/m/d-h.i.s/ --> */*/*/
        $links = array_reverse($archives);
        if (count($links) < $ini['blog_nposts']) $ini['blog_nposts'] = count($archives);
        $links = array_slice($links, 0, $ini['blog_nposts']);
        foreach ($links as $link) {
            $post = file_get_contents($link.'index.md');
            $Parsedown = new Parsedown();
            echo '<div class="post">';
            print('<div class="timestamp">'.$link.'</div>');
            echo $Parsedown->text($post);
            print('<br>');
            echo '<form action="./" method="get" enctype="multipart/form-data">
<button type="submit" id="View" name="submit" value="View">View</button>
<button type="submit" id="Edit" name="submit" value="Edit">Edit</button>
<button type="submit" id="Duplicate" name="submit" value="Duplicate">Duplicate</button>
<button type="submit" id="Delete" name="submit" value="Delete">Delete</button>
<button type="submit" id="Restamp" name="submit" value="Restamp">Restamp</button>

<input type="file" class="File_to_upload" name="File_to_upload" />
<input type="submit" class="Upload" name="submit" value="Upload" />

<input type="hidden" id="Body" name="body" value="Body" />
<input type="hidden" id="Link" name="link" value="'.$link.'" />
</form></div>';
        }
        print('<div class="archives">');
        print('<h2>Archives</h2><br />');
        foreach ($archives as $archive) {
            $file = fopen($archive.'index.md', 'r');
            print('<form action="./" method="get">
<button type="submit" id="View" name="submit" value="View">'.$archive.'<br />'.fgets($file).'</button>
<input type="hidden" id="Link" name="link" value="'.$archive.'" />
</form>');
            fclose($file);
        }
        print('</div>');



    }
}
?>

</body></html>
