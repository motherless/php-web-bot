Motherless PHP Web Bot Library
==============================

This library makes it easy for PHP developers to create Motherless bots. Motherless bots respond to triggers written in shouts, comments, board posts, and group forum posts.

How It Works
------------
Running a bot on Motherless is simple. You register a bot application on motherless.com (registration url coming soon) with your bot's trigger, eg "+tip", and a callback URL. When the trigger is used in a shout, comment, etc, Motherless will send a POST request to the callback URL with the content. Your bot then processes and responds to the request.

No special software is required. Not even the software provided here. These classes are provided as a convenience and learning tool. You are free to write your bot in your preferred language.


Installation
------------
Checkout the git repository, or simply copy the WebBot.php class to your own project source directory.


Usage
-----
The index.php script gives an example of creating a simple Motherless bot, which responds to the +quote trigger with a random quote.


Registering Your Bot
--------------------
Once you create your own bot, you need to register it with Motherless to have it called
when triggered. More details coming soon.