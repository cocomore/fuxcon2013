Out CakePHP implementation uses an external [Markdown plugin](https://github.com/Hyra/markdown). This includes a view helper that is used on the project detail pages process the Markdown markup. Here is an excerpt from app/View/Projects/view.ctp:

{% highlight html %}
<div class="span4">
    <h1 class="title-content"><?php echo $project['Project']['title']; ?></h1>
    <div class="about-content"><?php echo Markdown($project['Project']['about']); ?></div>
</div>
{% endhighlight %}

The second place where text processing occurs is on project lists. Here we use the built-in [Text helper](http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html) to shorten the teaser texts. Here is an excerpt from app/View/Projects/index.ctp:

{% highlight html %}
<p class="about"><?php echo $this->Text->truncate($project['Project']['about'], /*length*/100, array('exact' => FALSE)); ?></p>
{% endhighlight %}
