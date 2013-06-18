Pictures are a built-in field type in Drupal. They can be added to a content type in the admin interface:

![Drupal picture field]({{ site.url }}/fuxcon2013/img/drupal-project-fields-picture.png)

There are quite some settings which can be adjusted for image fields. Here are some that we tweaked:

![Drupal picture field]({{ site.url }}/fuxcon2013/img/drupal-picture-properties.png)

Aside from the image field, the derived picture styles must be configured:

![Drupal image styles]({{ site.url }}/fuxcon2013/img/drupal-image-styles.png)

Once these are configured, We still have to determine which derivative to show on project detail pages and in the project lists. The *medium* derivative is chosen for project detail pages:

![Drupal picture detail style]({{ site.url }}/fuxcon2013/img/drupal-picture-detail-style.png)

The *thumbnail* size for the project list is chosen in the configuration of the list:

![Drupal picture list style]({{ site.url }}/fuxcon2013/img/drupal-picture-list-style.png) 

Derived images can now be used in templates. Here is an excerpt from file sites/all/themes/fuxcon2013/node--project.tpl.php:

{% highlight php %}
<div class="thumbnail picture-content">
  <?php echo render($content['field_picture']); ?>
</div>
{% endhighlight %}

The image style is chosen by Drupal according to context: *medium* for project detail pages, *thumbnail* for project lists.
