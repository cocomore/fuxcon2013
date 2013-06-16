Our implementation does not use hand-written code for the business logic at all but relies on Drupal core or extensions like the Views module to provide it.

Here is a screen shot from the view definition for the project list in Drupal's admin inteface:

![Drupal Admin Views]({{ site.url }}/fuxcon2013/img/drupal-admin-view.png)

The Views module needs to be installed as an extension. This can conveniently be done using the Drush shell:

{% highlight bash %}
mkdir sites/all/modules/{custom,contrib}
drush download views
drush enable views
{% endhighlight %}

This downloads Views into the directory sites/all/modules/contrib/views and enables it.

As with content types, views can be exported as code to more easily manage them. However, we don't do this in our implementation.
