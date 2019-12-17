Time-Tracking
================================================
Time-Tracking is a simple command line tool for project timer trakcing.

# Getting Start
## Composer Library
```sh
composer require mediashare/time-tracking
```
## Usage

 - Run the commands as:
```sh
./time-tracking.phar start "Update Application"
./time-tracking.phar commit "Filesystem operations"
./time-tracking.phar status
./time-tracking.phar end
```

## Create Module (.sh)
```sh
# ./src/TimeTracking/Modules/ (default path)
#!/bin/sh
git add . 
git status
git commit -a -m "%message%"
```

## Creating a PHAR

 - Download and install [box][5]:
```sh
curl -LSs https://box-project.github.io/box2/installer.php | php
chmod +x box.phar
mv box.phar /usr/local/bin/box
```
 - Update the project phar config in box.json
 - Create the package:
```sh
box build
```
 - Run the commands:
```sh
./time-tracking.phar start "Update Application"
./time-tracking.phar commit "Filesystem operations"
./time-tracking.phar status
./time-tracking.phar end
```

## License

Cilex is licensed under the MIT license.

[1]: http://symfony.com
[2]: http://silex.sensiolabs.org
[3]: http://cilex.github.com/get/cilex.phar
[4]: http://cilex.github.com/documentation
[5]: https://box-project.github.io/box2/
