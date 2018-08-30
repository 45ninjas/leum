# Leum
[![Documentation Status](https://readthedocs.org/projects/leum/badge/?version=latest)](https://leum.readthedocs.io/en/latest/?badge=latest)

Leum is replacing my old and outdated *snuffy* and *redcat-media* sites.

Leum is designed to organize media like **TV Shows**, **Movies**, **Music** and **Pictures** on a web-server. It's not trying to be Plex or OwnCloud. Just a media viewer for my home server that I could access over the internet if I need.



[screenshot 1](https://tomp.id.au/files/2018-08/7ea6f569-cf3e-47cd-b277-afbd4d5869a4.png)

[screenshot 2](https://tomp.id.au/files/2018-08/dfd27b7c-4e0e-408f-aa2a-0ac7d8a21ecd.png)

[screenshot 3](https://tomp.id.au/files/2018-08/99ef5f33-4fd6-4cc8-b1f4-451b248a9c21.png)

[screenshot 4](https://tomp.id.au/files/2018-08/d5618227-f4cd-4809-afff-459373aa15dc.png)



**Current Features**

- A tagging system where you can filter media by tags.
- Thumbnail generation.
- User authentication and authorization (users roles and permissions)
- Admin pages for Users, Permissions, Media and Tags.
- View media in a lightbox.



**Future Features**

- Plugin support.
- A plugin to support TV shows, series and episodes.
- A plugin to support Movies.
- A plugin to support Music and albums.
- Multiple types of media with their own viewers.
- Nested media items.
- Multiresolution support. (720p, 480p, 360p)



**Features I can only dream about**

- Nice Chromecast support.
-  feature complete API. Leum has been written from the get-go with an API, however due to time constraints it's not anywhere near as complete or usable as the application it'self.
- Upgraded to VMC... not *view-and-controller* and model.
- Documentation for the API and core leum features.
- Ability to get media within leum. kissanime, yts, tpb, kisstoons?



### Installation

I recommend you don't install leum in it's current state, It's defiantly not as streamlined as Wordpress or Drupal.

If you absolutely MUST use leum I advise you contact me or brush up on your PHP skills.



**A brief overview of the setup process.**

1. Create a database.
2. Setup a symlink that's accessible from the web server that points to your server's media. (make it so you can access your movies from the web server).
3. Install FFMPEG on your server. (for thumbnails)
4. Edit the config files.
5. *setup.php*
6. Create a user by adding one to the database manually (might have to disable password verification so you can change your password)

Importing media is not as streamlined as I would like. You can try to use the import tools. I would suggest reading the code to understand how it works.
