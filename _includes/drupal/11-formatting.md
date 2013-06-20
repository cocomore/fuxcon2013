Drupal has an elaborate, extensible system of text formatters that process text formatting when saving content objects. Into this, we install the [markdown module](https://drupal.org/project/markdown):

{% highlight bash %}
drush --root=`pwd`/drupal dl markdown
{% endhighlight %}

After activating this module. we can include the Markdown filter in a new text format:

![Drupal text formats]({{ site.url }}/fuxcon2013/img/drupal-textformatters.png)

This format includes some configuration for the Markdown filter:

![Drupal Markdown filter]({{ site.url }}/fuxcon2013/img/drupal-markdownfilter.png)

This format is now available on project edit forms:

![Drupal Markdown filter]({{ site.url }}/fuxcon2013/img/drupal-markdown-edit-form.png)

The use of a shortened teaser for project lists is selected in the View definition:

![Drupal teaser formatter]({{ site.url }}/fuxcon2013/img/drupal-teaser-formatter.png)
