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
     * Constructor
     */
    public function __construct()
    {
        $this->params = "POST" === self::REQUEST_METHOD ? $_POST : $_GET;
    }

    /**
     * Process the incoming request
     */
    public function process()
    {
        if (self::REQUEST_METHOD === $_SERVER["REQUEST_METHOD"]) {
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
     */
    protected abstract function parse();

    /**
     * Any text the bot adds to parsed content should be run through this method
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
     * @return string
     */
    protected function getWrappedName()
    {
        return "[bot-name]{$this->name}[/bot-name]";
    }

    /**
     * Returns the name of the bot
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
     * @return string
     */
    protected function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * Returns the content
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
     * @return string
     */
    protected function getContentType()
    {
        return $this->content_type;
    }

    /**
     * Returns the content hash
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
     * @return array
     */
    protected function getMember()
    {
        return $this->member;
    }

    /**
     * Sends a successful response to the request
     *
     * @param string $content The parsed content
     * @param string $status  The parse status
     * @return bool
     */
    protected function sendContent($content, $status = "Success!")
    {
        if (!$this->sent) {
            $this
                    ->setStatusCode(200)
                    ->setHeader("X-Bot-Status",  $status);
            echo $content;
            $this->sent = true;
        }

        return true;
    }

    /**
     * Sends an error response to the request
     *
     * @param string $content The error message
     * @param string $status  The parse status
     * @return bool
     */
    protected function sendError($content, $status = "Error!")
    {
        if (!$this->sent) {
            $this
                    ->setStatusCode(418)
                    ->setHeader("X-Bot-Status",  $status);
            echo $content;
            $this->sent = true;
        }

        return true;
    }

    /**
     * Sends response to not replace the content
     *
     * @param string $status  The parse status
     * @return bool
     */
    protected function sendSkip($status = "Skipped!")
    {
        if (!$this->sent) {
            $this
                    ->setStatusCode(204)
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
        header(" ", true, $code);
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
        $value = urlencode($value);
        header("{$name}: {$value}");
        return $this;
    }
}