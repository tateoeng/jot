<?php
print('
        <form action="'.$this_page.'" method="post" enctype="multipart/form-data">
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
            <input type="hidden" id="archive" name="archive" value="'.$archive.'" /></form>');

?>