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

    private $keywords = array(
        array(
            array('paragraphs'),
            'rest.html#paragraphs',
        ),
        array(
            array('inline markup', 'emphasis', 'literals', 'bold', 'italics'),
            'rest.html#inline-markup',
        ),
        array(
            array('lists', 'quotes', 'line blocks'),
            'rest.html#lists-and-quote-like-blocks',
        ),
        array(
            array('source code', 'code example', 'code'),
            'rest.html#source-code',
        ),
        array(
            array('tables'),
            'rest.html#tables',
        ),
        array(
            array('hyperlinks', 'links'),
            'rest.html#hyperlinks',
        ),
        array(
            array('headlines', 'headers', 'sections'),
            'rest.html#sections',
        ),
        array(
            array('directives', 'caution', 'note', 'seealso', 'tip'),
            'rest.html#directives',
        ),
        array(
            array('images'),
            'rest.html#images',
        ),
        array(
            array('toc tree', 'toctree'),
            'markup/toctree.html',
        ),
        array(
            array('versionadded', 'version added'),
            'markup/para.html#directive-versionadded',
        ),
        array(
            array('roles', 'doc'),
            'markup/inline.html',
        ),
        array(
            array('references', 'ref'),
            'markup/inline.html#cross-referencing-syntax',
        ),
        array(
            array('indices', 'index'),
            'markup/misc.html#index-generating-markup',
        ),
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
