#!/bin/bash

cat <<EOS
Welcome to the installation of the feature definitions of the project site 
for the FUxCon 2013 workshop.

This software and documentation is released under 
the GNU General Public License (version 3). 
Please review the license in LICENSE.txt.

This script will download and setup the Behat software. 

EOS

read -p "Ready to proceed? [y]/n " reply
if [ "x$reply" != "x" -a "x$reply" != "xy" ]
then
  echo "Please type \"y\" or simply press ENTER to proceed with the installation"
  exit
fi

echo "Loading external dependencies ..."
curl -s http://getcomposer.org/installer | php
php composer.phar install

cat <<EOS
Done.

The next step is to install one of the four framework implementations

* https://github.com/cocomore/fuxcon2013_cakephp
* https://github.com/cocomore/fuxcon2013_django
* https://github.com/cocomore/fuxcon2013_drupal
* https://github.com/cocomore/fuxcon2013_symfony

... and run some feature validations by calling

    bin/behat

or 

    bin/behat --tags=new

This assumes that you have a website at http://cakephp.fuxcon2013.local 
You can change this URL in behat.yml

As a start, please browse through the features in directory features/
or head over to our website http://cocomore.github.io/fuxcon2013 for some background.

-- Olav
EOS
