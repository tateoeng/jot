# jot
small personal blogging engine

Jot requires the presence of [`Parsedown.php`](https://github.com/erusev/parsedown). Place `Parsedown.php` in the same writable web folder as `index.php`, `jot.ini` and `style.css`. Navigate to the desired web directory in a terminal and issue `sudo chown -R www-data:www-data /var/www/path/to/directory`. [Find `php.ini`](https://tecadmin.net/where-is-php-ini/) and [ensure that file upload is enabled](https://www.w3schools.com/php/php_file_upload.asp). Point a browser at the install, and you have a blog.

Jot is not intended for creating public-facing blogs and has no particular security features.
