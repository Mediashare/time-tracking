# Time Tracking
Time-Tracking is a simple command line tool for project timer tracking.
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
  ./time-tracking.phar timer:start                 Start Time Tracking
  ./time-tracking.phar timer:commit <message>      Commit Time Tracking
  ./time-tracking.phar timer:stop                  Stop Time Tracking
  ./time-tracking.phar timer:status                Status Time Tracking
  ./time-tracking.phar timer:end                   End Time Tracking (Archive session)
  ./time-tracking.phar upgrade               Download latest version of Time Tracking
```
## Build
[Box2](https://github.com/box-project/box2) used for .phar generation from php project. 
```bash
box build
```