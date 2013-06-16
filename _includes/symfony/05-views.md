Symfony provides its own template language called Twig which very closely resembles the Django template language. Here is an excerpt from the project list in src/FUxCon2013/ProjectsBundle/Resources/views/Projects/index.html.twig:

{% highlight html %}
{% raw %}
{% extends 'FUxCon2013ProjectsBundle::layout.html.twig' %}
{% block content %}
     <div class="row-fluid marketing projects">
    {% for column in columns %}
            <div class="span{{ width }}">
          {% for project in column %}
            <div class="project">
                <h4>
                    <a href="{{ path('_project', { 'id': project.id } ) }}">{{ project.title }}</a>
                </h4>
                <a href="{{ path('_project', { 'id': project.id } ) }}">
                    <img src="{{ thumbnail([ '/images/projects/', project.id, '.png' ] | join, '200x200') }}">
                </a>
                <p>{{ project.about | truncate }}</p>
            </div>
          {% endfor %}
          </div>
    {% endfor %}
     </div>
    {% include 'FUxCon2013ProjectsBundle::_paginate.html.twig' with { 'default_page': '_projects' } %}
{% endblock %}
{% endraw %}
{% endhighlight %}

Symfony uses named routes to generate URIs through the path() function. Loop and conditional constructs look like their Python cousins.  

This template uses extension to embed its markup into a common layout. The general structure of our layout file src/FUxCon2013/ProjectsBundle/Resources/views/layout.html.twig looks like this:

{% highlight html %}
{% raw %}
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <title>{% block title %}Projects{% endblock %}</title>
    <link href="{{ asset('bundles/fuxcon2013/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('bundles/fuxcon2013/css/sites.css') }}" rel="stylesheet">
  </head>

  <body>
    <div class="container-narrow">
      {% for flashMessage in app.session.flashbag.get('notice') %}
          <div class="flash-message">{{ flashMessage }}</div>
      {% endfor %}

      {% block content %}
      {% endblock %}

    </div> <!-- /.container-narrow -->
  </body>
</html>
{% endraw %}
{% endhighlight %}
 
The template views/Projects/index.html.twig provides overwrites for the blocks defined in this layout file.

Twig can be extended with custom tags and functions. We provide a new function thumbnail() and a filter truncate through such an extension. The extension lives in file src/FUxCon2013/ProjectsBundle/Twig/Extension/FUxCon2013Extension.php:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Twig\Extension;

use Symfony\Bundle\TwigBundle\Extension\AssetsExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FUxCon2013Extension extends \Twig_Extension
{
  private $container;

  public function __construct(ContainerInterface $container)
  { $this->container = $container; }

  public function getFunctions()
  {
    return array(new \Twig_SimpleFunction('thumbnail', array($this, 'thumbnail')));
  }

  public function getFilters()
  {
    return array(new \Twig_SimpleFilter('truncate', array($this, 'truncate')));
  }

  /**
   * Provide a new template function that generates a cacheable, derived image
   */
  public function thumbnail($path, $size = null)
  {
    // some hairy code to generate the derived image
  }

  /**
   * Provide a filter that truncates $text at word borders
   */
  public function truncate($text, $length = 150)
  {
    if (strlen($text) < $length) {
      return $text;
    }
   
    $text = substr($text, 0, $length);
    $blank = strrpos($text, ' ');
    if (FALSE === $blank) {
      $text = '';
    }
    else {
      $text = substr($text, 0, $blank);
    }
    return $text . ' ...';
  }

  public function getName()
  { return 'fuxcon2013_extension'; }
}
{% endhighlight %}


To be recognized, it needs to be registered with the system in app/config/config.yml like so:

{% highlight yaml %}
services:
    fuxcon2013.twig.fuxcon2013_extension:
        class: FUxCon2013\ProjectsBundle\Twig\Extension\FUxCon2013Extension
        tags:
            - { name: twig.extension }
        arguments: [ @service_container ]
{% endhighlight %}

Now that we have covered the basic building blocks of a model-view-controller architecture, we go on to describe the features specific to our requested features, namely user accounts, tagging, picture uploading and scaling, creating and updating projects, Markdown formatting, and finally the generation of test data.  
