In Drupal, the basic infrastructure for tagging goes under the name of taxonomies which has been a core module basically from day one of Drupal's inception as a powerful community system.

Since the introduction of *entities* in Drupal 7, similar to nodes, users, and comments, taxonomy terms have been promoted to the status of *entity* and can this be extended with fields. They are very well documented in the [Drupal Online Handbook](https://drupal.org/documentation/modules/taxonomy).

In our humble project, we don't use much of the power of taxonomies in Drupal. Basically´, we use the pre-configured *Tags* vocabulary and configure it to be applicable to our *project* content type by adding an appropriate field to it:

![Drupal project fields]({{ site.url }}/fuxcon2013/img/drupal-project-fields.png)

With this

![Drupal tag display]({{ site.url }}/fuxcon2013/img/drupal-topics.png)

... is entered as

![Drupal tag entry]({{ site.url }}/fuxcon2013/img/drupal-topics-edit.png)

Drupal even makes this field into an ajax autocomplete widget if configured this way in the content type.