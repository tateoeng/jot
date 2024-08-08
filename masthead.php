<?php
print('
    <form action="./" method="post" enctype="multipart/form-data">
        <button type="submit" id="home" name="submit" value="home">Home</button>
        <button type="submit" id="archives" name="submit" value="archives">Archives</button>
        <button type="submit" id="manage" name="submit" value="manage">Manage</button></form>
    <h1><a href="./">'.$blog_name.'</a></h1>
    <h5>'.$blog_tagline.'</h5>');


?>