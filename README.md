# LifePress

LifePress is an open source self-hosted lifestreaming platform built on the [CodeIgniter](http://codeigniter.com/) PHP framework. LifePress is a fork of the great, but no longer supported, [Sweetcron](https://code.google.com/p/lifepress/) software by Yong Fook. 

## What Has Changed

LifePress has been rewritten to work on CodeIgniter 2.1. Sweetcron was written on CI 1.6. LifePress is still backwards compatible with Sweetcron (see upgrading); the database schema and plugins/theme architecture are still the same. A lot of the code has been cleaned up to meet CI coding standards. For more info see the commit logs.

## Installation

1. In application/config/ rename config-sample.php to config.php
2. Open config.php in a text editor and replace http://www.your-site.com/ with your full site url, including trailing slash.
3. Create a new database on your server.
4. In application/config/ rename database-sample.php to database.php
5. Open database.php in a text editor and fill in the username, password and database name.
6. That's it! Now if you go to your website you should see instructions for installation.

### Installing In A Subdirectory

If you want LifePress in a sub folder (e.g. example.com/lifestream/), you will need to make these extra changes:

1. In config.php make sure the base\_url is the full url path to your LifePress install, i.e. including subfolder.
2. Open the .htaccess file found at the base of LifePress and change RewriteBase / to RewriteBase /<YOUR\_SUBFOLDER>

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

## Contribute/Issues

If you find a bug in LifePress please file an Issue. Make sure there isn't already an issue for that bug. Before you create a pull request please file an issue so the community can discuss the fix/enhancement and this way if the bug has already been fixed or is not plausible your not wasting your time.
