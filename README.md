# Dead Watcher

Dead Watcher is a service designed to monitor heartbeats from trading bots and cancel their orders if no signals are received within a specified time frame. This ensures that orphaned orders do not remain in the order book, protecting against potential financial losses due to market fluctuations.

## Description

### Purpose
In our trading system, if a bot instance crashes or stops functioning, its orders remain in the order book. This poses a risk: if the price of an asset (e.g., BTC) moves significantly up or down, users could profitably execute these orphaned orders, leading to financial losses. Dead Watcher addresses this issue by actively monitoring bot activity and cleaning up orders when a bot becomes unresponsive.

### Functionality
- **Heartbeat Monitoring**: Dead Watcher listens for heartbeat signals from active trading pairs every 120 seconds (configurable via `DeadWatcherConfig.php`). These signals are sent to the `/dead-watcher/heartbeat` endpoint.
- **Dynamic Pair Registration**: It does not rely on a predefined list of pairs. Instead, when a signal is received for a pair (e.g., `BTC_ETH`), Dead Watcher registers it dynamically and begins monitoring.
- **Asynchronous Order Cancellation**: Built on ReactPHP, Dead Watcher performs order cancellations asynchronously for a pair more than 120 seconds after no heartbeat is received, ensuring near-instantaneous responses rather than slow synchronous operations.
- **Redundancy**: For increased reliability, you can deploy multiple Dead Watcher instances, with their URLs specified in the `config.php` file of the main trading service.

 **Heartbeat Signals**: If `dead_watcher` is true, the trading bot should send a POST request to each URL in `dead_watcher_urls` every 120 seconds with the payload: `{"pair": "BTC_USDT", "bot_id": 5, "timestamp": <unix_timestamp>}`.

## Requirements
- PHP 8.1+
- Composer
- Docker (optional, for containerized deployment)

## Local Setup (Dev, Port 5503)
1. Navigate to the project directory:
   ```
   cd dead-watcher
   ```
2. Install dependencies:
   ```
   composer install
   ```
3. Run the service:
   ```
   php src/DeadWatcher.php
   ```

## Docker Setup
1. Navigate to the project directory:
   ```
   cd dead-watcher
   ```
2. Install dependencies:
   ```
   composer install
   ```
3. Launch in development environment (port 5503):
   ```
   docker-compose up -d bot-dead-watcher-dev
   ```
4. Launch in demo environment (port 6503):
   ```
   docker-compose up -d bot-dead-watcher-demo
   ```

## Endpoint
### POST /dead-watcher/heartbeat
- **Request Body**: `{"pair": "ETH_BTC", "bot_id": 5, "timestamp": 1743566264}`
- **Response**:
  - 200 OK: `{"status": "ok"}` (heartbeat accepted for bot_id 5)
  - 200 OK: `{"status": "ignored"}` (heartbeat ignored for other bot_ids)
  - 400 Bad Request: `{"error": "Invalid request body"}` (invalid payload)
  - 404 Not Found: `{"error": "Not found"}` (wrong endpoint)

## Configuration
- **Port**: Configured via the `DEAD_WATCHER_PORT` environment variable (default: 5503 for dev).
- **Trade Server URL**: Set via the `TRADE_SERVER_URL` environment variable (default: http://195.7.7.93:18080 for dev, http://164.68.117.90:18080 for demo).
- **Heartbeat Timeout**: Defined in `src/DeadWatcherConfig.php` as `$heartbeatTimeout` (default: 120 seconds).

## Logs
Logs are output to the console (in CLI mode) and written to `logs/dead-watcher.log` in the project root.

## Testing
1. Send a test heartbeat:
   ```
   curl -X POST http://localhost:5503/dead-watcher/heartbeat \
     -H "Content-Type: application/json" \
     -d '{"pair": "ETH_BTC", "bot_id": 5, "timestamp": 1743566264}'
   ```
2. Check logs:
   ```
   tail -f logs/dead-watcher.log
   ```