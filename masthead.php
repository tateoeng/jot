<?php
print('
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" class="home" name="submit" value="home">Home</button>
            <button type="submit" class="archives" name="submit" value="archives">Archives</button>
            <button type="submit" class="manage" name="submit" value="manage">Manage</button></p></form>
    <h1><a href="./">'.$blog_name.'</a></h1>
    <h5>'.$blog_tagline.'</h5>');


?>