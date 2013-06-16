Django configures URLs for an app in its file urls.py. Here is an excerpt from our projects/urls.py:

{% highlight python %}
urlpatterns = patterns('projects.views',
  url(r'^$', 'index', name='project_list'),
)

urlpatterns += patterns('',
  url(r'^project/(?P<pk>\d+)/$', ProjectDetailView.as_view(), name='project'),
  url(r'^project/add/$', ProjectAddView.as_view(), name='add_project'),
  url(r'^project/edit/(?P<pk>\d+)/$', ProjectEditView.as_view(), name='edit_project'),

  url(r'^user/', include(django.contrib.auth.urls)),
)
{% endhighlight %}

URLs are named to be able to generated named URLs in views. Views can be methods in our own implementation (e.g. the function projects.views.index() ) or can be classes from other apps. It is also possible to mount while ranges of URLs under a common root as is done in our example for the user handling.

Here is the view function for listing projects in file projects/views.py:

{% highlight python %}
GRID_COL = 12
NO_COL = 3
PER_PAGE = 5

def index(request):
  project_list = Project.objects.all()
  paginator = Paginator(project_list, PER_PAGE)

  page = request.GET.get('page')

  try:
    projects = paginator.page(page)

  except PageNotAnInteger:
    # If page is not an integer, deliver first page.
    projects = paginator.page(1)

  except EmptyPage:
    # If page is out of range (e.g. 9999), deliver last page of results.
    projects = paginator.page(paginator.num_pages)

  columns = [[] for x in range(0, NO_COL)]
  for i, project in enumerate(projects):
    col = i % NO_COL;
    columns[col].append(project);

  tags = Project.tags.most_common()

  return render(request, 'projects/index.html', {
    'body_class': 'projects-index',
    'columns': columns,
    'projects': projects,
    'tags': tags,
    'width': GRID_COL / NO_COL,
  })
{% endhighlight %}

Pagination uses the class Paginator from the system component django.core.paginator.
