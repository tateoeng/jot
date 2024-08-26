<?php
print('
    <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
        <p>
            <button type="submit" class="delete" name="submit" value="delete">Delete</button>
            <button type="submit" class="duplicate" name="submit" value="duplicate">Duplicate</button>
            <button type="submit" class="restamp" name="submit" value="restamp">Restamp</button>
            <input type="text" class="new_timestamp" name="new_timestamp" value="'.$archive.'" />
            <button type="submit" class="adjust" name="submit" value="adjust">Adjust</button>
            <br />
            <button type="submit" class="view" name="submit" value="view">View</button>
            <button type="submit" class="edit" name="submit" value="edit">Edit</button>
            <input type="file" class="file_to_upload" name="file_to_upload" />
            <input type="submit" class="upload" name="submit" value="Upload" />
            <input type="hidden" class="archive" name="archive" value="'.$archive.'" /></p></form>
            
');

?>