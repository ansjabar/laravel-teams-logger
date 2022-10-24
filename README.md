# laravel-teams-logger

Laravel handler to log errors to Microsoft Teams using Incoming Webhook connector.

## Installation

Require this package with composer.

```bash
$ composer require ansjabar/laravel-teams-logger
```

## Integration

```bash
$ php artisan vendor:publish --provider="AnsJabar\LaravelTeamsLogger\LoggerServiceProvider"
```

Add this configuration to `config/logging.php` file


```php
'teams' => [
    'driver'    => 'custom',
    'via'       => \AnsJabar\LaravelTeamsLogger\LoggerChannel::class,
    'level'     => 'debug',
    'url'       => env('TEAMS_LOGGING_URL'),
    'name'      => 'The Project' // Optional: 
],
```

After added configs to your `config/logging.php` file, add `TEAMS_LOGGING_URL` variable to your `.env` file with connector url from your microsoft teams connector. Please read [microsoft teams](https://docs.microsoft.com/en-us/microsoftteams/platform/concepts/connectors/connectors-using) document to find your connector url.

## Usage

To send a simple error message to teams channel, you can use script below:

```php
Log::channel('teams')->error('Error message');
```
To log all you application errors automatically, add `teams` to the default `stack` channel

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'teams'],
    ],
],
```
## License

This laravel-teams-logger package is available under the MIT license. See the LICENSE file for more info.
