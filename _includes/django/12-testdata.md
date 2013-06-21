In our Django implementation, we use the app *autofixture* that we install in the usual way:

{% highlight bash %}
source env/bin/activate 
pip install django-autofixture
{% endhighlight %}

... and register it with Django in file projects/settings.py:

{% highlight python %}
INSTALLED_APPS = (    
  'autofixture',    
)
{% endhighlight %}

Whith that, we have a new verb in the manage.py shell that we can use to generate test data:

{% highlight bash %}
python manage.py loadtestdata projects.Project:50
{% endhighlight %}

Currently, there are not special provisions to re-generate test data during the running of tests from Behat.