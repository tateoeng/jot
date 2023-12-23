<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<?php
include('Parsedown.php');





if (isset($_POST['blog_timezone'])) {
    date_default_timezone_set($_POST['blog_timezone']);
}






if (isset($_POST['submit']) && $_POST['submit'] == 'Set_up') {
    if (!file_exists('jot.ini')) {
        $file = fopen('jot.ini', 'w');
        // fwrite($file, 'Jot_loaded = "Jot.ini loaded"'.PHP_EOL);
        // fwrite($file, 'blog_number_posts = "3"'.PHP_EOL);
        fwrite($file, 'blog_name = "'.$_POST['blog_name'].'"'.PHP_EOL);
        fwrite($file, 'blog_tagline = "'.$_POST['blog_tagline'].'"'.PHP_EOL);
        fwrite($file, 'blog_timezone = "'.$_POST['blog_timezone'].'"'.PHP_EOL);
        fwrite($file, 'blog_timestamp = "'.$_POST['blog_timestamp'].'"'.PHP_EOL);
        fwrite($file, 'link_timestamp = "'.$_POST['link_timestamp'].'"'.PHP_EOL);
        fwrite($file, 'blog_nposts= "'.$_POST['blog_nposts'].'"'.PHP_EOL);
        fclose($file); 
    } else {
        $file = fopen('jot.ini', 'w');
        fwrite($file, $_POST['body']);
        fclose($file);
    }
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Save_stylesheet') {
    $file = fopen('style.css', 'w');
    fwrite($file, $_POST['body']);
    fclose($file);
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Preferences') {
    $ini = file_get_contents('jot.ini');
    echo '<div class="feature">
<form action="./" method="post">
<textarea name="body">'.$ini.'</textarea>
<p>&nbsp;</p>
<button type="submit" id="Set_up" name="submit" value="Set_up">Update</button>
</form>
</div>';
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Stylesheet') {
    $style = file_get_contents('style.css');
    echo '<div class="feature">
<form action="./" method="post">
<textarea name="body">'.$style.'</textarea>
<br />
<button type="submit" id="Save_stylesheet" name="submit" value="Save_stylesheet">Update</button>
</form>
</div>';
}





if (isset($_POST['submit']) && $_POST['submit'] == 'New_post') {
    echo '<form class="feature" action="./" method="post">
<p><textarea name="body"></textarea></p>
<p><button type="submit" id="Publish" name="submit" value="Publish">Publish</button></p>
</form>';
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Publish') {
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
    $new_timestamp = date($ini['link_timestamp']);
    if (!is_dir($new_timestamp)) mkdir($new_timestamp, 0755, true);
    if (!file_exists($new_timestamp.'index.md')) {
        $file = fopen($new_timestamp.'/index.md', 'w');
        fwrite($file, $_POST['body']);
        fclose($file); 
    } 
}





if (isset($_POST['submit']) && $_POST['submit'] == 'View') {
    $post = file_get_contents($_POST['link'].'index.md');
    $Parsedown = new Parsedown();
    print('<div class="feature">');
    echo $Parsedown->text($post);
    print($_POST['link']);
    echo '<form action="./" method="post">
<button type="submit" id="Edit" name="submit" value="Edit">Edit</button>
<button type="submit" id="Duplicate" name="submit" value="Duplicate">Duplicate</button>
<button type="submit" id="Delete" name="submit" value="Delete">Delete</button>
<button type="submit" id="Restamp" name="submit" value="Restamp">Restamp</button>
<input type="file" class="File_to_upload" name="the_file" />
<input type="submit" class="Upload" name="submit" value="Upload" />
<input type="hidden" id="Link" name="link" value="'.$_POST['link'].'" />
</form>';
    print('</div>'.PHP_EOL);
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Edit') {
    $post = file_get_contents($_POST['link'].'index.md');
    echo '<form action="./" method="post">
<textarea name="body">'.$post.'</textarea>
<br />
<button type="submit" id="Save" name="submit" value="Save">Save</button>
<input type="hidden" id="Link" name="link" value="'.$_POST['link'].'" />
</form>
<hr />';
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Save') {
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
    $file = fopen($_POST['link'].'index.md', 'w');
    fwrite($file, $_POST['body']);
    fclose($file);
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Duplicate') {
    // get link from previous entry
    $link = $_POST['link'];
    // set timezone
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
    // make new link from default timestamp form
    $new_link = date($ini['link_timestamp']);
    $new_link = $new_link.'/';
    // make new directory from new link
    // UNCOMMENT NEXT LINE FOR PRODUCTION
    if (!is_dir($new_link)) mkdir($new_link, 0755, true);
    // copy files unchanged from old directory to the new
    // find all files in old directory
    $files = glob($link.'*');
    foreach ($files as $file) {
        $tmp = explode('/', $file);
        $filename = array_pop($tmp);
        copy($file, $new_link.$filename);
    }
    // find .md documents in old directory
    $files = glob($link.'*.md');
    // for each .md file found, copy the contents of the post while replacing old with new link
    foreach ($files as $file) {
        // get filename
        $filename = explode('/', $file);
        $filename = array_pop($filename);
        // rewrite contents of post to new post
        // first, get the old contents
        $post = file_get_contents($link.$filename);
        // replace old link with new link
        $new_post = str_replace($link, $new_link, $post);
        // write the new contents to the new file
        $file = fopen($new_link.$filename, 'w');
        fwrite($file, $new_post);
        //close the file
        fclose($file);
    }
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Delete') {
    $link = $_POST['link'];
    $files = glob($link.'*');
    foreach ($files as $file) { unlink($file); }
    $links = explode('/', $link);
    array_pop($links);
    $link = implode('/', $links);
    $count = count(scandir($link));
    while ($count == 2) {
        rmdir($link);
        $links = explode('/', $link);
        array_pop($links);
        $link = implode('/', $links);
        $count = count(scandir($link));
    }    
}






if (isset($_POST['submit']) && $_POST['submit'] == 'Restamp') {
    // get link from previous entry
    $link = $_POST['link'];
    // parse ini file and set timezone based on preference
    $ini = parse_ini_file('jot.ini');
    date_default_timezone_set($ini['blog_timezone']);
    // make new link from default timestamp form
    $new_link = date($ini['link_timestamp']);
    $new_link = $new_link.'/';
    // make new directory from new link
    if (!is_dir($new_link)) mkdir($new_link, 0755, true);

    // find all files in old directory
    $files = glob($link.'*');
    // copy files unchanged from old directory to the new
    foreach ($files as $file) {
        $tmp = explode('/', $file);
        $filename = array_pop($tmp);
        copy($file, $new_link.$filename);
    }

    // find .md documents in old directory
    $files = glob($link.'*.md');
    // for each .md file found, copy the contents of the post while replacing old with new link
    foreach ($files as $file) {
        // get filename
        $filename = explode('/', $file);
        $filename = array_pop($filename);
        // rewrite contents of post to new post
        // first, get the old contents
        $post = file_get_contents($link.$filename);
        // replace old link with new link
        $new_post = str_replace($link, $new_link, $post);
        // write the new contents to the new file
        $file = fopen($new_link.$filename, 'w');
        fwrite($file, $new_post);
        //close the file
        fclose($file);
    }

    
    // once again, find all files in old directory
    $files = glob($link.'*');
    // delete each file
    foreach ($files as $file) { unlink($file); }

    // recursively delete old directory and all empty parent directories
    $links = explode('/', $link);
    array_pop($links);
    $link = implode('/', $links);
    $count = count(scandir($link));
    while ($count == 2) {
        rmdir($link);
        $links = explode('/', $link);
        array_pop($links);
        $link = implode('/', $links);
        $count = count(scandir($link));
    }    
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Upload') {
    // adapted from https://www.w3schools.com/php/php_file_upload.asp
    print($_POST['link']);
    $target_dir = $_POST['link'];
    $target_file = $target_dir . basename($_FILES["File_to_upload"]["name"]);
    move_uploaded_file($_FILES["File_to_upload"]["tmp_name"], $target_file);
}





if (isset($_POST['submit']) && $_POST['submit'] == 'Show_archives') {
    // etc.
}





// if the ini file does not exist, show init form.
if (!file_exists('jot.ini')) {
    echo '<h1>welcome to jot</h1>
<p id="problem_p">please fill out the form below to configure your blog</p>
<form action="./" method="post">
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
        echo '<form action="./" method="post">
<p><textarea name="body"></textarea></p>
<p><button type="submit" id="Publish" name="submit" value="Publish">Publish</button></p>
</form>';


	// if archives exist, show the blog.
    } else {
		echo '<div class="masthead"><div class="blog_name"><h1><a href="./">'.$ini['blog_name'].'</a></h1><h4 class="tagline">'.
$ini['blog_tagline'].'</h6></div>
<form action="./" method="post">
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
            echo '<form action="./" method="post" enctype="multipart/form-data">
<button type="submit" id="View" name="submit" value="View">View</button>
<button type="submit" id="Edit" name="submit" value="Edit">Edit</button>
<button type="submit" id="Duplicate" name="submit" value="Duplicate">Duplicate</button>
<button type="submit" id="Delete" name="submit" value="Delete">Delete</button>
<button type="submit" id="Restamp" name="submit" value="Restamp">Restamp</button>
<br />
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
            print('<form action="./" method="post">
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
