# Time Tracking
Time-Tracking is a simple command line tool for project timer management. PHP 8.2 is require.
## Installation
### Binary
```bash
curl --output time-tracking https://raw.githubusercontent.com/Mediashare/time-tracking/master/time-tracking
chmod 755 time-tracking
sudo cp time-tracking /usr/local/bin/time-tracking
```
#### Usage
```bash
  time-tracking timer:list                List all timer
  time-tracking timer:start               Start timer
  time-tracking timer:stop                Stop timer
  time-tracking timer:status              Status timer
  time-tracking timer:archive             Archive timer
  time-tracking timer:remove              Remove timer

  time-tracking timer:commit <message>    New commit
  time-tracking timer:commit:edit <id>    Edit commit
  time-tracking timer:commit:remove <id>  Remove commit
  
  time-tracking upgrade                   Download latest version of Time Tracking
```
### Composer
```bash
composer require mediashre/time-tracking
composer install
```
#### Usage
```bash
  ./vendor/bin/time-tracking timer:list                List all timer
  ./vendor/bin/time-tracking timer:start               Start timer
  ./vendor/bin/time-tracking timer:stop                Stop timer
  ./vendor/bin/time-tracking timer:status              Status timer
  ./vendor/bin/time-tracking timer:archive             Archive timer
  ./vendor/bin/time-tracking timer:remove              Remove timer

  ./vendor/bin/time-tracking timer:commit <message>    New commit
  ./vendor/bin/time-tracking timer:commit:edit <id>    Edit commit
  ./vendor/bin/time-tracking timer:commit:remove <id>  Remove commit
  
  ./vendor/bin/time-tracking upgrade                   Download latest version of Time Tracking
```
## Build
[Box2](https://github.com/box-project/box2) used for .phar generation from php project. PHP 7.4 is require.
```bash
box build
# or
./build
```
