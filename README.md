# Time Tracking
Time-Tracking is a simple command line tool for project timer management.
## Installation
### Manuel
```bash
curl -O https://raw.githubusercontent.com/Mediashare/time-tracking/master/time-tracking.phar
chmod 755 time-tracking.phar
sudo cp time-tracking.phar /usr/bin/time-tracking
```
### Composer
```bash
composer require mediashare/time-tracking
vendor/bin/time-tracking.phar
```
## Usage
```bash
  ./time-tracking.phar timer:list             List all timer
  ./time-tracking.phar timer:start            Start timer
  ./time-tracking.phar timer:stop             Stop timer
  ./time-tracking.phar timer:status           Status timer
  ./time-tracking.phar timer:archive          Archive timer
  ./time-tracking.phar timer:remove           Remove timer

  ./time-tracking.phar timer:commit <message> New commit
  ./time-tracking.phar timer:commit:edit      Edit commit
  ./time-tracking.phar timer:commit:remove    Remove commit
  
  ./time-tracking.phar upgrade                Download latest version of Time Tracking
```
## Build
[Box2](https://github.com/box-project/box2) used for .phar generation from php project. 
```bash
box build
```