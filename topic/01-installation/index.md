---
title: Installation
name: installation
layout: topic
permalink: /installation/
---

Neither of the frameworks installs into global directories of the operating system. This was not always the case. Python extensions used to be installed into a central library directory. The same was true for PHP when using the popular Pear extension manager. 

However, this approach caused much grieve when multiple installed packages had different version requirements so generally it is no longer used. In addition, disk space nowadays is cheap. Rather, PHP and Python now use package managers (composer for PHP, virtualenv for Python) to install required extensions locally for each project.  

## CakePHP
{% include cakephp/01-installation.md %}

## Django
{% include django/01-installation.md %}

## Drupal
{% include drupal/01-installation.md %}

## Symfony
{% include symfony/01-installation.md %}
