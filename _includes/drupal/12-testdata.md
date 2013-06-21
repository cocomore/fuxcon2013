Our Drupal installation uses *devel_generate*, a submodule of *Devel* to (re-)generate test data: 

![Drupal devel generate]({{ site.url }}/fuxcon2013/img/drupal-devel-generate.png)

There are quite a view settings that allow to fine-tune the generation of test data:

![Drupal devel generate]({{ site.url }}/fuxcon2013/img/drupal-devel-generate-settings.png)

With that, it is easily possible to generate test projects:

![Drupal devel generate]({{ site.url }}/fuxcon2013/img/drupal-generated-testdata.png)

To integrate test data generation into an automatic test framework, the integration into the Drush command line framework is very useful:

![Drupal devel generate drush]({{ site.url }}/fuxcon2013/img/drupal-generate-drush.png)

However, our current implementation has no provision to automate test data generation when running Behat tests.

  