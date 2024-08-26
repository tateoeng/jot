<?php
print('
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
');
    print('<a href="#" id="top" onclick="scrollToTop();return false;">^</a>');
    print('</body></html>');
?>