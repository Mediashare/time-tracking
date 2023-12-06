# Time Tracking
Time-Tracking is a simple command line tool for project timer management. **PHP >=8.1 is required.**
## Installation
### Composer
```bash
composer global require mediashre/time-tracking
```
### Binary
```bash
curl --output time-tracking https://raw.githubusercontent.com/Mediashare/time-tracking/master/time-tracking
chmod 755 time-tracking
sudo cp time-tracking /usr/local/bin/time-tracking
```
## Usage
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
  
  time-tracking timer:upgrade             Download latest version of Time Tracking
```
## Contributing
### Box install
[Box2](https://github.com/box-project/box) used for binary generation from php project. **PHP >=8.1 is required.**
```bash
composer global require humbug/box
box
```
### Usage
```bash
composer dump-env dev
box compile
```
