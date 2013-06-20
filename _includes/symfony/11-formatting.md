For our Symfony implementation, the [knplabs/knp-markdown-bundle](https://github.com/KnpLabs/KnpMarkdownBundle) bundle provides the required functionality to convert Markdown markup into HTML. Once that is installed and registered with the kernel in app/AppKernel.php:

{% highlight php %}
<?php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        // ... register all core bundles
        $bundles[] = new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle();
    }
}
{% endhighlight %}

This bundle provides a new Twig filter, that we can use in our project detail template, here in an excerpt from file src/FUxCon2013/ProjectsBundle/Resources/views/Project/show.html.twig:

{% highlight html %}
{% raw %}
<p>{{ project.about | markdown }}</p>
{% endraw %}
{% endhighlight %}

Shortening of teaser texts does not require an extension. It can directly be used in our template for project list, , here in an excerpt from file src/FUxCon2013/ProjectsBundle/Resources/views/Project/index.html.twig:

{% highlight html %}
{% raw %}
<p>{{ project.about | truncate }}</p> 
{% endraw %}
{% endhighlight %}
