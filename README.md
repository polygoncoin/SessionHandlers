# SessionHandler
Collection of Mostly used Session Handlers

- Supports File / MySql / Redis / Memcached based Session Handlers
- Supports Readonly mode as well for all the above mentioned Session Handlers

## Example

```PHP
<?php
include __DIR__ . '/SessionHandlers/Session.php';

// Initialise Session Handler
Session::initSessionHandler('File');
// Session::initSessionHandler('MySql');
// Session::initSessionHandler('Redis');
// Session::initSessionHandler('Memcached');

// Start session in readonly mode
// Use when user is already logged in and we need to authorise the client cookie.
Session::start_readonly();
if (isset($_SESSION)) {
    print_r($_SESSION);
}

// Start session in normal (read/write) mode.
// Use once client is authorised and want to make changes in $_SESSION
Session::start_rw_mode();
$_SESSION['id'] = 1;

```

## Database Table for MySql

```SQL
CREATE TABLE IF NOT EXISTS `sessions` (
    `sessionId` CHAR(32) NOT NULL,
    `lastAccessed` INT UNSIGNED NOT NULL,
    `sessionData` TEXT,
    PRIMARY KEY (`sessionID`)
) ENGINE=InnoDB;
```