Our implementation uses Django's built-in authentication app and middleware for managing user accounts. There is a page in the documentation on how Django stores passwords in the database.
For object-level permissions, we write a custom authentication backend and register it as well:

{% highlight bash %}
virtualenv .env
source .env/bin/activate
pip install django-object-permissions 
{% endhighlight %}

The complete declaration for these components in projects/settings.py look like this:

{% highlight python %}
MIDDLEWARE_CLASSES = (
    'django.contrib.auth.middleware.AuthenticationMiddleware',
)

INSTALLED_APPS = (
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'object_permissions',
)

AUTHENTICATION_BACKENDS = (
    'projects.models.PermBackend',
)
{% endhighlight %}

We mount the views for user management by including the routes in projects/urls.py:

{% highlight python %}
urlpatterns += patterns('',
  url(r'^user/', include(django.contrib.auth.urls)),
)
{% endhighlight %}

The authentication backend in file projects/models.py implements a new method has_perm() and looks like this:

{% highlight python %}
class Project(models.Model):
  class Meta:
    permissions = (
      ('can_edit', "Can edit project"),
    )

class PermBackend(ModelBackend):
  def has_perm(self, user, perm, obj=None):
    "Owner and admin can edit"

    if perm != 'can_edit' or obj is None:
      return super(PermBackend, self).has_perm(user, perm, obj)
   
    if user.is_superuser:
      return True
   
    return obj.user.id == user.id
{% endhighlight %}

This backend enhances the method has_perm() on user objects. We can therefore now check for object-level permissions in our edit view (file projects/views.py):

{% highlight python %}
class ProjectEditView(UpdateView):

  def dispatch(self, request, *args, **kwargs):
    project = self.get_object()

    if not request.user.has_perm('can_edit', project):
      messages.error(request,
        'You are not allowed to edit project #' + str(project.id)
      )
      return HttpResponseRedirect(reverse('project', args=(project.id,)))
   
    return super(ProjectEditView, self).dispatch(request, *args, **kwargs)
{% endhighlight %}
