# LifePress

LifePress is an open source self-hosted lifestreaming platform built on the [CodeIgniter](http://codeigniter.com/) PHP framework. LifePress is a fork of the great, but no longer supported, [Sweetcron](https://code.google.com/p/sweetcron/) software by Yong Fook. 

## What Has Changed

LifePress has been rewritten to work on CodeIgniter 2.1. Sweetcron was written on CI 1.6. LifePress is still backwards compatible with Sweetcron (see upgrading); the database schema and plugins/theme architecture are still the same. A lot of the code has been cleaned up to meet CI coding standards. For more info see the commit logs.

## Installation

1. In application/config/ rename config-sample.php to config.php
2. In config.php replace http://www.your-site.com/ with your full site url, including trailing slash.
3. In config.php set a [encryption key](http://codeigniter.com/user_guide/libraries/encryption.html) using a random 32 characters string.
4. Create a new database on your server.
5. In application/config/ rename database-sample.php to database.php
6. Open database.php in a text editor and fill in the username, password and database name.
7. That's it! Now if you go to your website you should see instructions for installation.

### Installing In A Subdirectory

If you want LifePress in a sub folder (e.g. example.com/lifestream/), you will need to make these extra changes:

1. In config.php make sure the base\_url is the full url path to your LifePress install, i.e. including subfolder.
2. Open the .htaccess file found at the base of LifePress and change ```RewriteBase /``` to ```RewriteBase /<YOUR_SUBFOLDER>```

### Pseudo Cron vs Cron Job

LifePress can import items from your feeds either through Pseudo Cron (default) or a cron job (preferred). If you wish to use a cron job you will need to set up a manual cron job with your hosting provider. The options panel provides a switch between both and provides the url you will have to curl if you use a cron job. 

**Pseudo Cron** - zero configuration. The only disadvantage is that once every 30 minutes, one visitor to your website may experience a slow load time as they will have triggered the automatic import.

**Cron Job** - you will need to set up a manual cron job on your server. The advantage is that updates happen without any user ever knowing (i.e. no slowdowns). The other advantage is that you can increase how frequently LifePress imports new items. 

## Upgrading

1. Backup all files, just in case!
2. Save your .htaccess, config.php and database.php to a separate location.
3. Save your themes folder to a separate location.
4. Overwrite your LifePress install with the new LifePress files you downloaded.
5. Copy your .htaccess, config.php and database.php and themes files back in.
6. Quickly check in case there have been any changes to the above 3 files since last upgrade.

## Plugins

Plugins improve how LifePress imports data from different websites. LifePress is pretty good at automatically detecting images, video and tags, but when it can't a plugin can often fix that. Plugins are in `application/plugins`.

There are two "hooks" - functions where we can manipulate feed data:

* At import using `pre_db()` - before the item is put into the database.
* At display using `pre_display()` - before the item is rendered in a user's browser.

It's best to make your changes in `pre_display()` when possible, because `pre_db()` makes permanent changes to the data as it gets stored to the db. At import is suitable for making tweaks to data that we know we want to be permanent, like stripping out unwanted data, or setting certain items to "draft" status. At display is suitable for simple tweaks to the available data, like the youtube plugin setting `$item->item_data['video']` based on the youtube link in the item. 

### Creating a plugin

Plugin file names must be the website's url for the feed it was built for, replacing periods with an underscore. E.g. youtube.com plugin is `youtube_com.php`.

```
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Youtube_com {
    function pre_db($item)
    {
        $item->item_status = 'draft';
        return $item;
    }

    function pre_display($item)
    {
        $item->item_data['video'] = '<iframe width="640" height="360" src="' . $item->item_data['permalink'] . '"></iframe>';
        return $item;
    }
}
?>
```

Available item attributes are:

```
$item->item_status //status of item. "publish" by default, you can set to "draft"
$item->item_date  //date of the item in unix timestamp
$item->item_title //title of the item
$item->item_permalink //permalink back to the origin of the item
$item->item_content = //content of the item
$item->item_name = //url-safe name of the item, created from the item title
```

#### Storing Custom Data

Item objects also feature an `$item->item_data` array.  This can be used to store custom values.  It contains extra data about the item so be careful you do not overwrite anything you might want later.  Existing `item_data` values are:

```
$item->item_data['title'] //raw title before it was cleaned for $item->item_title
$item->item_data['permalink'] //raw permalink of item
$item->item_data['content'] //raw content
$item->item_data['enclosures'] //data found in the item's rss "enclosure" section
$item->item_data['categories'] //data found in the item's rss "categories" section﻿  
$item->item_data['tags'] //tags LifePress was able to associate with the item﻿  
$item->item_data['image'] //image that LifePress was able to associate with the item
```

You can store completely custom data by simply adding to the `item_data` array:

```
$item->item_data['foo'] = 'bar';
```

## Themes

Themes can be found in ```application/views/themes```.

### Creating a Theme

The simplest way to create a new theme is to copy the sandbox theme folder and rename it to your desired theme name. Folder names cannot contain spaces. Once you create a new folder in the themes folder, it becomes available as a selectable theme in the admin dashboard.

A theme folder should always contain these files:

    _activity_feed.php //the main activity list of your theme
    _header.php //the header of your site
    _footer.php //the footer of your site
    _sidebar.php //the sidebar of your site
    home.php //the index page of your site
    items.php //the item page of your site (search / tag results)
    single.php //the single item view page
    rss_feed.php //formatting for the rss feed
    main.css //the css for your site

#### Displaying Imported Items

For your site's index page and item pages, an $items object is provided to the theme templates. See the _activity_feed.php file in the sandbox and boxy themes for examples on how to loop through $items and customize them based on feed they came from.

#### Easily Access Media In Your Themes

LifePress' theme API has three functions for getting media:

```
$item->get_image()
$item->get_video()
$item->get_audio()
```

## Contribute/Issues

If you find a bug in LifePress please file an Issue. Make sure there isn't already an issue for that bug. Before you create a pull request please file an issue so the community can discuss the fix/enhancement and this way if the bug has already been fixed or is not plausible your not wasting your time.
