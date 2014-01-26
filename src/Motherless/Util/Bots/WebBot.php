<?php
namespace Motherless\Util\Bots;

/**
 * Motherless web bot
 */
abstract class WebBot
{
    /**
     * The request method to listen for, switch to "GET" for testing.
     */
    const REQUEST_METHOD = "POST";

    /**
     * The name of the bot
     * @var string
     */
    private $name = null;

    /**
     * The bot trigger
     * @var string
     */
    private $trigger = null;

    /**
     * The content to parse
     * @var string
     */
    private $content = null;

    /**
     * The content type
     * @var string
     */
    private $content_type = null;

    /**
     * The content hash
     * @var string
     */
    private $content_hash = null;

    /**
     * The member who wrote the content
     */
    private $member = null;

    /**
     * POST/GET data received by the bot
     * @var array
     */
    private $params = [];

    /**
     * Did the bot send a response?
     * @var bool
     */
    private $sent = false;

    /**
     * Process the incoming request
     *
     * @param array $params The request parameters
     */
    public function process(array $params = array())
    {
        if (self::REQUEST_METHOD === $_SERVER["REQUEST_METHOD"] || !empty($params)) {
            if (!empty($params)) {
               $this->params = $params;
            } else {
                $this->params = "POST" === self::REQUEST_METHOD ? $_POST : $_GET;
            }

            $required = array("name", "trigger", "content", "content_type", "content_hash", "member");
            $missing  = array_diff($required, array_keys($this->params));
            if (empty($missing)) {
                $this->name         = $this->params["name"];
                $this->trigger      = $this->params["trigger"];
                $this->content      = $this->params["content"];
                $this->content_type = $this->params["content_type"];
                $this->content_hash = $this->params["content_hash"];
                $this->member       = $this->params["member"];

                $this->parse();
                if (!$this->sent) {
                    $this->sendSkip("Bot did not send a response.");
                }
            }
        }
    }

    /**
     * Parse the content
     *
     * This is where your put the logic for your bot. Once the class has finished processing the incoming request
     * from motherless, it will call this class. From here you should do any kind of processing and filter on the
     * shout, comment, etc, and use one of the send*() methods to respond to the motherless request.
     */
    protected abstract function parse();

    /**
     * Any text the bot adds to parsed content should be run through this method
     *
     * Any text your bot adds to the content should be wrapped in [bot-wrap] bbcode tags. This method wraps the
     * $text value in those tags. The wrapped text requirements may change in the future, so you should get used
     * to using this method to wrap the text instead of adding the [bot-wrap] tags yourself.
     *
     * @param string $text The text being added
     * @return string
     */
    protected function getWrappedText($text)
    {
        return "[bot-wrap]{$text}[/bot-wrap]";
    }

    /**
     * Returns the name of the bot wrapped in special bbcode
     *
     * When placing the name of the bot inside of content, the name should be wrapped in [bot-name] bbcode tags.
     * This method returns the name of the bot with those tags. The wrapped text requirements may change in the future,
     * so you should get used to using this method to wrap the text instead of adding the [bot-name] tags yourself.
     *
     * @return string
     */
    protected function getWrappedName()
    {
        return "[bot-name]{$this->name}[/bot-name]";
    }

    /**
     * Returns the name of the bot
     *
     * Returns the name of the bot as it was registered with motherless, for example "TipBot" or "QuoteBot".
     *
     * @return string
     */
    protected function getName()
    {
        return $this->name;
    }

    /**
     * Returns the trigger
     *
     * Returns the string which triggered motherless to called your bot, for example "+tip" or "+quote".
     *
     * @return string
     */
    protected function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * Returns the content
     *
     * Returns the full contents of the shout, comment, board post, or group forum post which contained the
     * bot trigger.
     *
     * @return string
     */
    protected function getContent()
    {
        return $this->content;
    }

    /**
     * Returns the content type
     *
     * Returns a string which helps identify the nature of the content. Some example return values are "shout" when
     * the content is coming from a shout, and "boards" when the content is coming from the boards.
     *
     * The content type will often contain additional information to help you identify the origin. For example the
     * method may return "comment+5FF7642", meaning the content is a comment on the upload "5FF7642".
     *
     * @return string
     */
    protected function getContentType()
    {
        return $this->content_type;
    }

    /**
     * Returns the content hash
     *
     * Returns a unique identifier for the content. The value can be used to prevent double-parsing the same content.
     *
     * @return string
     */
    protected function getContentHash()
    {
        return $this->content_hash;
    }

    /**
     * Returns the details of the member who wrote the content
     *
     * Returns an array which describes the member who wrote the content, and specifically included the bot trigger
     * in the content. When the content was written by an anonymous user of the site, the returned array will be
     * the array ["id" => 0, "username" => "anonymous"]. For other members the returned array will contain the
     * following keys.
     *
     *  "id"                    The id of the member
     *  "username"              The member username
     *  "time_joined"           Unix timestamp when the member joined the site
     *  "num_uploads"           The number of uploads the member has
     *  "num_favorited"         The number of the favorites the member has received
     *  "num_friends"           The number of friends the member has
     *  "gender"                The member's gender
     *  "sexuality"             The member's sexual orientation
     *  "favorite_porn"         The member's favorite type of porn
     *  "location"              The member's bio location
     *  "tagline"               The member's tagline
     *  "bio"                   The member's bio
     *  "is_gender_verified"    Whether the member's gender has been verified
     *  "url_avatar"            The url to the member's avatar
     *  "url_profile"           The url to the member's profile
     *
     * It's important to note these values are public on each members profile, and the details do not contain any
     * private information.
     *
     * @return array
     */
    protected function getMember()
    {
        return $this->member;
    }

    /**
     * Sends a successful response to the request
     *
     * You call this method from the parse() method to respond to the request from motherless. The content passed
     * to this method will replace the content of the shout, comment, etc.
     *
     * The status value is recorded but not shown. You will be able to audit your bots from the control panel on
     * motherless.com, and view the statuses your bot responded with.
     *
     * This method always returns true.
     *
     * @param string $content The parsed content
     * @param string $status  The parse status
     * @return bool
     */
    protected function sendContent($content, $status = "Success!")
    {
        if (!$this->sent) {
            $this->setStatusCode(200)
                 ->setHeader("X-Bot-Status",  $status);
            echo $content;
            $this->sent = true;
        }

        return true;
    }

    /**
     * Sends an error response to the request
     *
     * You call this method from the parse() method to respond to the request from motherless. The content passed
     * to this method will placed at the *bottom* of the shout, comment, etc in red as an error message. For example
     * when a member tries to tip another member with TipBot, and they have insufficient credits, TipBot places an
     * error message at the bottom of the content explaining why the tip can't be sent.
     *
     * The status value is recorded but not shown. You will be able to audit your bots from the control panel on
     * motherless.com, and view the statuses your bot responded with.
     *
     * This method always returns true.
     *
     * @param string $content The error message
     * @param string $status  The parse status
     * @return bool
     */
    protected function sendError($content, $status = "Error!")
    {
        if (!$this->sent) {
            $this->setStatusCode(418)
                 ->setHeader("X-Bot-Status",  $status);
            echo $content;
            $this->sent = true;
        }

        return true;
    }

    /**
     * Sends response to not replace the content
     *
     * You call this method from the parse() method to respond to the request from motherless. Calling this method
     * signals to motherless that your bot acknowledged the request, but has chosen to do nothing.
     *
     * The status value is recorded but not shown. You will be able to audit your bots from the control panel on
     * motherless.com, and view the statuses your bot responded with.
     *
     * This method always returns true.
     *
     * @param string $status  The parse status
     * @return bool
     */
    protected function sendSkip($status = "Skipped!")
    {
        if (!$this->sent) {
            $this->setStatusCode(204)
                 ->setHeader("X-Bot-Status",  $status);
            echo "";
            $this->sent = true;
        }

        return true;
    }

    /**
     * Sets the http status code
     *
     * @param int $code The status code
     * @return $this
     */
    private function setStatusCode($code)
    {
        if (!$this->isCommandLine()) {
            header(" ", true, $code);
        }
        return $this;
    }

    /**
     * Sets a header value
     *
     * @param string $name  The header name
     * @param string $value The header value
     * @return $this
     */
    private function setHeader($name, $value)
    {
        if (!$this->isCommandLine()) {
            $value = urlencode($value);
            header("{$name}: {$value}");
        }
        return $this;
    }

    /**
     * Returns true if php is running on the command line. False if not.
     *
     * @return bool
     */
    private function isCommandLine()
    {
        if(php_sapi_name() == "cli" && empty($_SERVER["REMOTE_ADDR"])) {
            return true;
        } else {
            return false;
        }
    }
}