<?php

namespace Wj\DocBot\Plugin;

use Wj\DocBot\SpreadSheetStack;
use Phoebe\Event\Event;
use Phoebe\Plugin\PluginInterface;

class SphinxHelpPlugin implements PluginInterface
{
    const TRIGGER_PATTERN = '/(?:sphinx|rest|restructured|rst) help (.+?)$/i';

    const SUCCESS_MESSAGE = 'You can find information in %s';
    const PROBLEM_MESSAGE = 'I cannot find that, take a look at sphinx-doc.org/contents.html';

    private $remarks = array(
        'Great job, %s!',
        'Way to go, %s!',
        '%s is amazing, and everyone should be happy this amazing person is around.',
        'I wish I was more like %s.',
        '%s, you are crazy, but in a good way.',
        '%s has a phenomenal attitude.',
        '%s is a great part of the team!',
        'I love %s\'s work today!',
    );

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'irc.received.PRIVMSG' => array('onMessage', 0)
        );
    }

    /**
     * @param Event $event
     */
    public function onMessage(Event $event)
    {
        $message = $event->getMessage();
        $matches = array();

        if ($message->isInChannel() && $message->matchText(self::TRIGGER_PATTERN, $matches)) {
            try {
                $link = $this->findLink($matches[1]);

                $event->getWriteStream()->ircPrivmsg(
                    $message->getSource(),
                    sprintf(
                        '%s: %s',
                        $message['nick'],
                        $link === false ? self::PROBLEM_MESSAGE : sprintf(
                            self::SUCCESS_MESSAGE,
                            'http://sphinx-doc.org/'.$link
                        )
                    )
                );
            } catch (\RuntimeException $e) {
                $event->getWriteStream()->ircPrivmsg(
                    $message->getSource(),
                    'Cannot read spreadsheat: '.$e->getMessage()
                );
            }
        }
    }

    protected function findLink($keyword)
    {
        foreach ($this->keywords as $k) {
            if (in_array($keyword, $k[0])) {
                return $k[1];
            }
        }

        return false;
    }
}
