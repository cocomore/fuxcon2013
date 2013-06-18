There are several [alternative extensions](https://www.djangopackages.com/grids/g/tagging/) to implement tagging in Django. Unfortunately, the most popular one is also no longer maintained. We chose [django-tagging](https://www.djangopackages.com/packages/p/django-taggit/) and installed it with

{% highlight bash %}
source env/bin/activate
pip install django-tagging
{% endhighlight %}

As usual, the extension is registered as an installed app in projects/settings.py:

{% highlight bash %}
INSTALLED_APPS = (
    'taggit',
)
{% endhighlight %}

As explained in the [documentation](https://github.com/alex/django-taggit#readme) of the extension, it relies on a field in the model that is a TaggableManager. With that in place, *taggit* automatically converts a comma separated list of tags into the proper entries in the two tables *taggit_tag* and *taggig_taggeditem* that the extension adds to the database. With this

![Django tag display]({{ site.url }}/fuxcon2013/img/django-topics.png)

... is entered as

![Django tag entry]({{ site.url }}/fuxcon2013/img/django-topics-edit.png)

