By enabling the markup app that comes with Django, we get Markup formatting in our Django implementation. This app relies on the Python library markdown that we install with pip:

{% highlight bash %}
source env/bin/activate 
pip install markdown
{% endhighlight %}

The markup app needs to be included in the Django settings as usual:

{% highlight python %}
INSTALLED_APPS = (    
  'django.contrib.markup',
)
{% endhighlight %}

With this, we can use markdown formatting in templates. Here is an excerpt from file templates/projects/detail.html:

{% highlight html %}
{% raw %}
{% load markup %}
<div class="about-content">{{ project.about|markdown }}</div>
{% endraw %}
{% endhighlight %}

Shortening of teaser texts can be done with a built-in template filter. Here is an excerpt from file templates/projects/index.html

{% highlight html %}
{% raw %}
<p>{{ project.about|truncatewords:"40" }}</p>
{% endraw %}
{% endhighlight %}
