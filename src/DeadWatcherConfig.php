<?php

namespace DeadWatcher;

class DeadWatcherConfig
{
    private $port;
    private $heartbeatTimeout = 60; // seconds
    private $tradeServerUrl;

    public function __construct()
    {
        $this->port = getenv('DEAD_WATCHER_PORT') ?: 5503; // By default dev port
        $this->tradeServerUrl = getenv('TRADE_SERVER_URL') ?: 'http://195.7.7.93:18080'; // Dev server by default
    }

    public function getPort(): int
    {
        return (int)$this->port;
    }

    public function getHeartbeatTimeout(): int
    {
        return $this->heartbeatTimeout;
    }

    public function getTradeServerUrl(): string
    {
        return $this->tradeServerUrl;
    }
}
