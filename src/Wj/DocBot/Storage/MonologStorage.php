<?php

namespace Wj\DocBot\Storage;

use Psr\Log\LoggerInterface;
use Phoebe\Plugin\UserInfo\StorageInterface;

class MonologStorage implements StorageInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function clear()
    {
        $this->logger->info('Registering to channel');
    }

    public function setUserMode($nickname, $channel, $mode)
    {
        $this->logger->info("Mode of '{$nickname}' changed to '{$mode}'");
    }

    public function getUserMode($nickname, $channel)
    {
        return null;
    }

    public function updateNickname($oldNickname, $nickname)
    {
        $this->logger->info("{$oldNickname} is now called {$nickname}");
    }

    public function removeUser($nickname, $channel = null)
    {
        $this->logger->info("{$nickname} left the channel");
    }

    public function getChannels($nickname)
    {
        return array('symfony-docs');
    }

    public function getUsers($channel)
    {
        return array();
    }

    public function getRandomUser($channel, $ignore = null)
    {
        return null;
    }
}
