In Django, templates can be application specific or global. Here is an excerpt from our file templates/index.html:

{% highlight html %}
{% raw %}
{% extends 'layout.html' %}
{% load markup %}

{% block content %}
  <div class="row-fluid marketing projects">
  {% for column in columns %}
    <div class="span{{ width }}">
    {% for project in column %}
      <div class="project">
        <h4>
          <a href="{% url 'project' project.id %}">
            {{ project.title }}
          </a>
        </h4>
        <a href="{% url 'project' project.id %}">
          {% if project.photo %}
            <img class="project-detail" src="{{ project.photo_medium.url }}">
          {% else %}
            <img class="project-detail" src="/static/img/ni.png">
          {% endif %}
        </a>
        <p>{{ project.about|truncatewords:"40" }}</p>
      </div>
    {% endfor %}
    </div>
  {% endfor %}
  </div>

  {% include '_paginate.html' with projects=projects %}  
{% endblock %}
{% endraw %}
{% endhighlight %}

Django has its own template language. In this example, the columns variable is provided by the view. Function calls to url use named routes with parameters to generate proper URIs. Scaled photos are declared in the model and can be accessed as ordinary model fields, e.g. project.photo_medium.url. The code for a paginator at the bottom of the page is included from another template file.
