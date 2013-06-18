In our Django implementation, pictures and derived picture formats are handled by the [django-imagekit](https://github.com/matthewwithanm/django-imagekit) extension. There would have been [some alternatives](https://www.djangopackages.com/grids/g/thumbnails/).

After installing with *pip* and registering in *INSTALLED_APPS* in the usual way, we can add special fields to our project model in file projects/models.py:

{% highlight python %}
class Project(models.Model):
  photo = models.ImageField(upload_to='project', blank=True, null=True)
  
  # derived images: https://github.com/matthewwithanm/django-imagekit#readme
  photo_thumbnail = ImageSpecField(source='photo',
    processors=[ResizeToFill(100, 100)],
    format='JPEG',
    options={'quality': 60}
  )
  photo_medium = ImageSpecField(source='photo',
    processors=[ResizeToFill(200, 200)],
    format='JPEG',
    options={'quality': 60}
  )
  photo_big = ImageSpecField(source='photo',
    processors=[ResizeToFill(380, 380)],
    format='JPEG',
    options={'quality': 60}
  )

  def admin_thumbnail(self):
    if self.photo:
      return '<img src="%s">' % self.photo_thumbnail.url
  admin_thumbnail.allow_tags = True
  
{% endhighlight %} 

Derived images are special fields that are filled during project save. These fields are not stored in the database. Only the path to the original photo is stored in a column in the database.

In addition to the fields, we also add a method that provides the admin interface with a thumbnail for the project list view.

Once these fields are added to the model, they can be accessed from templates like any other image field. Here is an excerpt from our project detail page in file templates/projects/detail.html:

{% highlight python %}
{% raw %}
{% if project.photo %}
<p class="thumbnail picture-content">
  <img src="{{ project.photo_big.url }}">
</p>
{% endif %}
{% endraw %}
{% endhighlight %}
