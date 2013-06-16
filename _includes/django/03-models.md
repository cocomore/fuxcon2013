Django follows a content-first approach. Once you have defined your models, you automatically get an API and a fully working admin interface that you can use to enter content into the database. Each app can have its own models. All model classes for an app are defined in the single file <em>app</em>/models.py

All fields are declared. You then create the database tables with the command line tool:

{% highlight python %}
manage.py syncdb 
{% endhighlight %}

Here is part of the model file app/projects/models.py from our Django model:

{% highlight python %}
class Project(models.Model):
  class Meta:
    verbose_name = 'Projekt'
    verbose_name_plural = 'Projekte'
    db_table = 'projects'
    get_latest_by = 'modified'
    ordering = ['title']

  user = models.ForeignKey(User)
  slug = models.SlugField()
  title = models.CharField(max_length=255)
  about = models.TextField(blank=True, null=True)
  photo = models.ImageField(upload_to='project', blank=True, null=True)
 
  # derived images implemented with extension django-imagekit:
  photo_thumbnail = ImageSpecField(source='photo', processors=[ResizeToFill(100, 100)],
    format='JPEG', options={'quality': 60}
  )
  photo_medium = ImageSpecField(source='photo', processors=[ResizeToFill(200, 200)],
    format='JPEG', options={'quality': 60}
  )
  photo_big = ImageSpecField(source='photo', processors=[ResizeToFill(380, 380)],
    format='JPEG', options={'quality': 60}
  )

  start_date = models.DateField(blank=True, null=True)
  end_date = models.DateField(blank=True, null=True)

  # tags implemented by extension django-taggit
  tags = TaggableManager(blank=True)

  created = models.DateTimeField(auto_now_add=True)
  modified = models.DateTimeField(auto_now=True)
 
  def __unicode__(self):
    return 'Project #' + str(self.id) + ': ' + self.title
   
  def admin_thumbnail(self):
    if self.photo:
      return '<img src="%s">' % self.photo_thumbnail.url
  admin_thumbnail.allow_tags = True
{% endhighlight %} 

From this, Django generates the admin interface. If you want to further specify aspects of your admin interface, you can do so in a separate file. Here is part of our projects/admin.py:

{% highlight python %}
class ProjectAdmin(admin.ModelAdmin):
  #date_hierarchy = 'start_date'
  list_display = ('title', 'start_date', 'end_date', 'admin_thumbnail')
  list_editable = ('start_date', 'end_date')
  prepopulated_fields = {'slug': ('title',)}
 
  fieldsets = (
    (None, {
      'fields': (
        'slug',
        'title',
        'photo',
        'about',
        'start_date',
        'end_date',
        'tags',
      ),
    }),
  )

admin.site.register(Project, ProjectAdmin)
{% endhighlight %}

Here are screenshots from the generated admin interface. This includes user accounts:

![Django Admin]({{ site.url }}/fuxcon2013/img/django-admin.png)

![Django Admin Project]({{ site.url }}/fuxcon2013/img/django-admin-detail.png)

