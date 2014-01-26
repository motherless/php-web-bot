<?php
use Motherless\Util\Bots\WebBot;

/**
 * Example creating your own motherless bot using PHP.
 *
 * This is QuoteBot. It responds to the +quote trigger in shouts, comments, board posts, and
 * group forum posts, and replaces the trigger with a random quote.
 */
include("src/Motherless/Util/Bots/WebBot.php");
class QuoteBot
    extends WebBot
{
    /**
     * Process the incoming request
     */
    protected function parse()
    {
        // $member is an array with the details of the motherless member who wrote the content.
        // @see WebBot::getMember() for more details.
        $member  = $this->getMember();

        // $content is the content, either a shout, comment, board post, or group forum post.
        // @see WebBot::getContent() for more details.
        $content = $this->getContent();

        // $name is the name of this bot with some special bbcode wrapped around it.
        // @see WebBot::getWrappedName() for more details.
        $name    = $this->getWrappedName();

        // $trigger is the text which triggered calling this bot.
        // @see WebBot::getTrigger() for more details.
        $trigger = $this->getTrigger();

        // $this->getWrappedText() wraps the bot's text in some special bbcode tags.
        // @see WebBot::getWrappedText() for more details.
        $quote   = $this->getRandomQuote();
        $quote   = $this->getWrappedText("
            {$name}:
            A quote for {$member['username']}
            [i]{$quote['quote']}[/i]
            - {$quote['author']}
        ");

        // Replace the trigger in the content with our random quote.
        $content = str_replace($trigger, $quote, $content);

        // Respond with the new content.
        // @see WebBot::sendContent() for more details.
        $this->sendContent($content);
    }

    /**
     * Returns a random quote
     *
     * @return array
     */
    private function getRandomQuote()
    {
        $quotes = [
            [
                "quote"  => "If you don't make mistakes, you're not working on hard enough problems. And that's a big mistake.",
                "author" => "Frank Wilczek"
            ],
            [
                "quote"  => "I cannot think well of a man who sports with any woman's feelings; and there may often be a great deal more suffered than a stander-by can judge of.",
                "author" => "Jane Austen"
            ],
            [
                "quote"  => "When life seems chaotic, you don't need people giving you easy answers or cheap promises. There might not be any answers to your problems. What you need is a safe place where you can bounce with people who have taken some bad hops of their own.",
                "author" => "Real Live Preacher"
            ]
        ];

        return $quotes[array_rand($quotes)];
    }
}

// Create the bot instance, and have it process requests from motherless.
$bot = new QuoteBot();
$bot->process();
