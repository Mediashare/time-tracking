# Time Tracking
Time-Tracking is a simple command line tool for project timer tracking.
## Installation
### Manuel
```bash
curl -O https://raw.githubusercontent.com/Mediashare/time-tracking/master/time-tracking.phar
chmod 755 time-tracking.phar
```
### Composer
```bash
composer require mediashare/time-tracking
vendor/bin/time-tracking.phar
```
## Usage
```bash
  ./time-tracking.phar start                 Start Time Tracking
  ./time-tracking.phar commit <message>      Commit Time Tracking
  ./time-tracking.phar stop                  Stop Time Tracking
  ./time-tracking.phar status                Status Time Tracking
  ./time-tracking.phar end                   End Time Tracking. (Archive session)
```
