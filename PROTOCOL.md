Motherless Web Bot Protocol
===========================

This document describes the protocol behind the web bot protocol.

Overview
--------
Motherless sends a POST request to a web service which has been registered at Motherless. The request will contain
the content of a shout, board post, upload comment, or group forum post. The response from the service will replace
the content on Motherless. The bot essentially acts as a content filter.


Request
-------
Upon finding the trigger for a web bot within site content, Motherless will send a POST request to the web bot. The
request will be application/x-www-form-urlencoded, and contain the following items:


* "name"         The name of the bot registered with the trigger.
* "trigger"      The bot trigger. For example "+tip" or "+quote".
* "content"      The content containing the trigger. This will be either a shout, board post, upload comment, or group forum post.
* "content_type" The type of content, for example "shout", "comment", or "boards".
* "content_hash" A unique hash for the content.
* "member"       The member that wrote the content. This item is a hash with the follow items:
    * "id"                    The id of the member
    * "username"              The member username
    * "time_joined"           Unix timestamp when the member joined the site
    * "num_uploads"           The number of uploads the member has
    * "num_favorited"         The number of the favorites the member has received
    * "num_friends"           The number of friends the member has
    * "gender"                The member's gender
    * "sexuality"             The member's sexual orientation
    * "favorite_porn"         The member's favorite type of porn
    * "location"              The member's bio location
    * "tagline"               The member's tagline
    * "bio"                   The member's bio
    * "is_gender_verified"    Whether the member's gender has been verified
    * "url_avatar"            The url to the member's avatar
    * "url_profile"           The url to the member's profile

Response
--------
The web bot should respond with one with one of the following HTTP status codes:

* 200 The response body is content which should completely replace the site content.
* 418 The response body is content which should be appended to the site content.

A response using any other status code will be discarded by Motherless.

The response may also contain the "X-Bot-Status" header, the value of which is recorded by Motherless, but not
shown on the site. Bot operators will be able to see these values when auditing their registered web bots.
