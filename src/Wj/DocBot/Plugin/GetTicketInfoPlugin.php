<?php

namespace Wj\DocBot\Plugin;

use Wj\DocBot\SpreadSheetStack;
use Phoebe\Event\Event;
use Phoebe\Plugin\PluginInterface;

class GetTicketInfoPlugin implements PluginInterface
{
    const TRIGGER_PATTERN = '/get info about(?: (?:tasks|issue|ticket))? #([0-9]+)/';

    const MESSAGE = '#%d [%s] %s {%s} %s';

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
                            self::MESSAGE,
                            $info[0],
                            $info[1],
                            $info[2],
                            empty($info[5]) ? 'FREE' : 'ASSIGNED',
                            empty($info[5]) ? null : $info[5].' in #'.$info[6]
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
