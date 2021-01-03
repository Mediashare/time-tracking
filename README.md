# Time Tracking
**[DEPRECATED] Go to [Gitlab Project](https://gitlab.marquand.pro/MarquandT/time-tracking)**

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
composer global require mediashare/time-tracking
echo 'export PATH="$PATH:$HOME/.composer/vendor/bin:$HOME/.config/composer/vendor/bin"' >> ~/.bashrc
source ~/.bashrc
time-tracking
```
## Usage
```bash
  time-tracking timer:list             List all timer
  time-tracking timer:start            Start timer
  time-tracking timer:stop             Stop timer
  time-tracking timer:status           Status timer
  time-tracking timer:archive          Archive timer
  time-tracking timer:remove           Remove timer

  time-tracking timer:commit <message> New commit
  time-tracking timer:commit:edit      Edit commit
  time-tracking timer:commit:remove    Remove commit
  
  time-tracking upgrade                Download latest version of Time Tracking
```
## Build
[Box2](https://github.com/box-project/box2) used for .phar generation from php project. 
```bash
box build
# or
./build
```
