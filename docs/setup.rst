Setup
=====

Installing leum is pretty straightforward.

It's broken down into a few steps.

1. Configure web-server.
2. Create symbolic links to media.
3. Set file and directory permissions.
4. Create and configure the database.
5. Modify the configuration files for leum.
6. Run ``/leum/setup.php`` using your browser.
7. Create an root account.


Configuring the web-server
--------------------------

If using symbolic links to store your media and thumbnails (highly recommended) you'll need to allow symbolic links in your site's configuration.

If you store all your media and thumbails in the content directory you can allow symlinks in only that directory.


Creating symbolic links
-----------------------

In ``leum.conf.php`` the ``MEDIA_DIR`` and ``THUMB_DIR`` settings set where leum stores it's media and thumbnails respectively. The content of these directories must bee accessible via the web server.

I strongly recommend using symbolic links.

My content directory looks like this.
::
	tom@redcat:/var/www/leum$ ls -l content/
	total 0
	lrwxrwxrwx 1 tom tom 17 Sep 16 13:47 media -> /mnt/raid1/media/
	lrwxrwxrwx 1 tom tom 24 Sep 16 13:47 thumbs -> /mnt/drive4/leum/thumbs/

Notice how media is pointing to ``/mnt/raid1/media/`` and thumbs to ``/mnt/drive4/leum/thumbs/``. These locations are on different drives the www directory.

Permissions
-----------

Leum needs to have access to write and create in ``/logs/`` and ``THUMB_DIR``.
Some plug ins require write access in ``MEDIA_DIR``.
The web-server must be allowed to serve the files in ``THUMB_DIR`` and ``MEDIA_DIR``.


Database
--------

You'll need to create a database for leum a good practice is to also create a user with full permissions to this database as well.

The ``conf/database.conf.php`` is where you provide leum with the username password host and name for the database.

Configuration
-------------

All configuration files are available in ``leum/conf/``. Leum loads the ``leum.conf.file``. All other configuration files included from this file using PHP's include_once.

You can enable and disable plug ins with the ``ACTIVE_PLUGINS`` array.

Setup.php
---------

Access the setup.php file through your browser. If all goes well the final message should be '*setup process complete with no errors*'.

If not. Read the errors or/and the logs provided in the logs directory.

Create user
-----------

You'll need to create a new user. 