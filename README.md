# Gravatar integration for Nextcloud

This plugin is a fork of https://github.com/weeman1337/nextcloud-gravatar.

**This app adds Gravatar support to your Nextcloud.**

Gravatar is an online service where users can upload an avatar linked to their email address.
This app checks the user's Gravatar on every login and sets the avatar image.

## Settings
You may configure the app to ask every user whether he wants to use Gravatar or not:  
![Gravatar app settings](/doc/settings.png "Gravatar app settings")

This results in a notification:  
![Gravatar notification](/doc/notification.png "Gravatar notification")

And a setting:  
![Gravatar user setting](/doc/user-setting.png "Gravatar user setting")

## Privacy note  
This app sends md5 hashed versions of the users' email addresses to Gravatar.
See also their privacy policy: https://automattic.com/privacy/ .
