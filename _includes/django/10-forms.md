In our Django implementation, the models already provide an API to read, create &amp; update, and delete domain objects, projects in our case.

URL patterns link to the responsible views:

{% highlight python %}
urlpatterns = patterns('',
  url(r'^project/(?P<pk>\d+)/$', ProjectDetailView.as_view(), name='project'),
  url(r'^project/add/$', ProjectAddView.as_view(), name='add_project'),
  url(r'^project/edit/(?P<pk>\d+)/$', ProjectEditView.as_view(), name='edit_project'),
{% endhighlight %}

These views are defined in file projects/views.py. Actually, these classes are only concerned with permissions. They inherit all the CRUD functionality directly fron built-in views in Django:

{% highlight python %}
class ProjectAddView(ProjectMixin, CreateView):
  pass

class ProjectEditView(ProjectMixin, UpdateView):
  pass
{% endhighlight %}

The ProjectMixin keeps non-owners from editing projects as explained [elsewhere]({{ site.url }}/fuxcon2013/accounts/#django).

With the support of this rather short template in templates/projects/project_form.html:

{% highlight python %}
{% raw %}
{% extends 'layout.html' %}

{% block content %}
{# http://blog.headspin.com/?p=541 #}
<form method="POST" enctype="multipart/form-data">
  <h3>Add new project</h3>
  {{ form }}{% csrf_token %}
  <div class="submit">
    <button id="save-button" class="btn" type="submit">Submit</button>
  </div>
</form>
{% endblock content %}
{% endraw%}
{% endhighlight %}

... the edit form looks like this

![Django edit form]({{ site.url }}/fuxcon2013/img/django-edit-form.png)
