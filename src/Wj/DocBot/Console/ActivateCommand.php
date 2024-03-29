<?php

namespace Wj\DocBot\Console;

use Wj\DocBot\Plugin;
use Wj\DocBot\Storage;
use Wj\DocBot\SpreadSheetStack;
use Phoebe\ConnectionManager;
use Phoebe\Connection;
use Phoebe\Event\Event;
use Phoebe\Plugin as PhoebePlugin;
use Phoebe\Plugin\PluginInterface;
use Monolog\Logger;
use Monolog\Handler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActivateCommand extends Command
{
    protected function configure()
    {
        $this->setName('docbot:run')
            ->setDescription('Starts the bot')
            ->addOption('nickname', null, InputOption::VALUE_REQUIRED, 'Who do you want to be?', 'DocSprintBot')
            ->addArgument('channels', InputArgument::IS_ARRAY, 'List of channels to join', array('symfony-docs'));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nickname = $input->getOption('nickname');
        $channels = $input->getArgument('channels');

        $connectionManager = $this->getConnectionManager();
        $connectionManager->addConnection($this->getFreenodeConnection($nickname, $channels));
        $connectionManager->run();
    }

    /**
     * @param string $nickname
     * @param array  $channels
     *
     * @return Connection
     */
    private function getFreenodeConnection($nickname, array $channels)
    {
        $connection = new Connection();
        $connection->setServerHostname('irc.freenode.net');
        $connection->setServerPort(6667);
        $connection->setNickname($nickname);
        $connection->setUsername($nickname);
        $connection->setRealname($nickname);

        $dispatcher = $connection->getEventDispatcher();

        foreach ($this->getPlugins() as $plugin) {
            $dispatcher->addSubscriber($plugin);
        }

        $dispatcher->addListener('irc.received.001', function (Event $event) use ($channels) {
            foreach ($channels as $channel) {
                $event->getWriteStream()->ircJoin('#'.$channel);

            }
        });

        return $connection;
    }

    /**
     * @return ConnectionManager
     */
    private function getConnectionManager()
    {
        return new ConnectionManager();
    }

    /**
     * @return PluginInterface[]
     */
    private function getPlugins()
    {
        $spreadsheatStack = new SpreadSheetStack('0AkTCyW0ZOR36dGdyOWRHVlRIcnpZa3hCYV9jM3hrTEE');
        $logger = new Logger('user logger');
        $logger->pushHandler(new Handler\StreamHandler(ROOT_DIR.'/data/'.date('ymdHi').'.log'));

        return array(
            new Plugin\IsTicketFreePlugin($spreadsheatStack),
            new Plugin\GetTicketInfoPlugin($spreadsheatStack),
            new Plugin\SphinxHelpPlugin(),
            new Plugin\CartoonPlugin(),
            new PhoebePlugin\UserInfo\UserInfoPlugin(new Storage\MonologStorage($logger)),
        );
    }
}
