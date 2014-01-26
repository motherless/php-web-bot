<?php
use Motherless\Util\Bots\WebBot;

class TestBot
    extends WebBot
{

    /**
     * Parse the content
     */
    protected function parse()
    {
        $member  = $this->getMember();
        $content = $this->getContent();
        $name    = $this->getName();
        $trigger = $this->getTrigger();

        $content = $this->getWrappedText("
            Testing Username '{$member['username']}'.
            Testing Trigger '{$trigger}'.
            Testing Name '{$name}'.
            Testing Content '{$content}'.
        ");
        $this->sendContent($content);
    }
}

class WebBotTest
    extends PHPUnit_Framework_TestCase
{
    protected static $params = array();

    public static function setupBeforeClass()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";
        self::$params = array(
            "name"         => "TestBot",
            "trigger"      => "+test",
            "content"      => "start +test end",
            "content_type" => "shout",
            "content_hash" => "35787965e471b7055ad899434b3ca8b9",
            "member"       => array(
                "id"                 => 151464,
                "username"           => "headz",
                "time_joined"        => 1223520388,
                "num_uploads"        => 5,
                "num_favorited"      => 2,
                "num_friends"        => 1,
                "gender"             => "male",
                "sexuality"          => "straight",
                "favorite_porn"      => "amateur",
                "location"           => "NYC",
                "tagline"            => "So, I'm pretty normal",
                "bio"                => "Don't call me. I'll call you.",
                "is_gender_verified" => 1,
                "url_avatar"         => "http://avatars.motherlessmedia.com/avatars/member/headz.gif",
                "url_profile"        => "http://motherless.com/m/headz"
            )
        );
    }

    /**
     * @covers Motherless\Util\Bots\WebBot::process
     */
    public function testProcess()
    {
        ob_start();
        $test_bot = new TestBot();
        $test_bot->process(self::$params);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertContains(
            "[bot-wrap]",
            $contents
        );
        $this->assertContains(
            "Testing Username 'headz'.",
            $contents
        );
        $this->assertContains(
            "Testing Trigger '+test'.",
            $contents
        );
        $this->assertContains(
            "Testing Name 'TestBot'.",
            $contents
        );
        $this->assertContains(
            "Testing Content 'start +test end'.",
            $contents
        );
    }
}
