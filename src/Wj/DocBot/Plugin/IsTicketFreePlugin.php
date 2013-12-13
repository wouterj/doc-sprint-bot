<?php

namespace Wj\DocBot\Plugin;

use Wj\DocBot\SpreadSheetStack;
use Phoebe\Event\Event;
use Phoebe\Plugin\PluginInterface;

class IsTicketFreePlugin implements PluginInterface
{
    const TRIGGER_PATTERN = '/is (?:tasks|issue|ticket) #([0-9]+) free/';

    const NOT_FREE_MESSAGE = 'Ticket #%d is not free, %s is working on it in #%s';
    const FREE_MESSAGE = 'Ticket #%d is free for you';

    protected $spreadsheat;

    public function __construct(SpreadSheetStack $spreadsheat)
    {
        $this->spreadsheat = $spreadsheat;
    }

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
                $info = $this->getTicketInfo($matches[1]);

                $event->getWriteStream()->ircPrivmsg(
                    $message->getSource(),
                    sprintf(
                        '%s: %s',
                        $message['nick'],
                        sprintf(
                            empty($info[5]) ? self::FREE_MESSAGE : self::NOT_FREE_MESSAGE,
                            $info[0],
                            $info[5],
                            $info[6]
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

    private function getTicketInfo($nr)
    {
        $data = $this->spreadsheat->getData();

        foreach ($data as $row) {
            if ($row[0] == $nr) {
                return $row;
            }
        }
    }
}
