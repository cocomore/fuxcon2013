The CakePHP implementation uses the [Tags Plugin](http://github.com/cakedc/tags/) to implement tagging on projects. This plugin implements a new [behavior](http://book.cakephp.org/2.0/en/models/behaviors.html) on the Project model. This is the top of our model in file app/Model/Project.php

{% highlight php %}
<?php
/**
 * Project Model
 *
 * @property User $User
 */
class Project extends AppModel {
  public $actsAs = array('Tags.Taggable', 'Containable');
  // more domain model goodness
}
{% endhighlight %}

The plugin adds two new tables *tags* and *tagging* and thus implements the m:n relationship between projects and tags. The plugin relies on a field *tags* in the model which it automatically links to. A user can enter tags as a comma separated list in the edit form which is converted to related tags behind the scenes by the plugin behavior:

![CakePHP tag display]({{ site.url }}/fuxcon2013/img/cakephp-topics.png)

... is entered as

![CakePHP tag entry]({{ site.url }}/fuxcon2013/img/cakephp-topics-edit.png)

