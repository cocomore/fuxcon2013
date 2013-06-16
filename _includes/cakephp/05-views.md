Views in CakePHP can be arranged in themes. We use this to define our design-specific templates file in directory app/View/Themed/Bootstrap. Each controller action has a corresponding view template, e.g. the file app/View/Projects/index.ctp. CakePHP uses plain PHP files as templates. Here is some example code from index.ctp:

{% highlight php %}
  <?php foreach ($columns as $column): ?>
    <div class="span<?php echo $width; ?>">
    <?php foreach ($column as $project): ?>
      <div class="project">
        <h4>
          <?php echo $this->Html->link($project['Project']['title'], array('action' => 'view', $project['Project']['id'])); ?>
        </h4>
        <?php echo $this->Html->link(
          $this->Thumbnail->render('project/' . $project['Project']['id'] . '.jpg', array(
            'width' => 200, 'height' => 200, 'resizeOption' => 'auto',
          )), array('action' => 'view', $project['Project']['id']), array('escape' => FALSE, 'class' => 'thumbnail')); ?>
        <p class="about"><?php echo $this->Text->truncate($project['Project']['about'], /*length*/100 + rand(1, 100), array('exact' => FALSE)); ?></p>
      </div>
    <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

<?php echo $this->element('paginate'); ?> 
{% endhighlight %}

$columns is the paginated list of projects, arranged into columns and passed from the controller. $this->Html is a view helper that we use here to generate links. $this->Thumbnail is a custom helper. This is defined in app/View/Helper/ThumbnailHelper.php and declared in app/Controller/AppController.php as

{% highlight php %}
<?php
class ProjectsController extends AppController { 
  public $helpers = array('Thumbnail', 'Markdown.Markdown');
  // …
}
{% endhighlight %}

Markdown formatting for project detail pages is provided by the Markdown plugin. The helpers Html and Text are always available and don't have to be declared this way.   

To provide a common design, the output of templates is generally embedded into layout files. The basic structure of our layout file app/View/Themed/Bootstrap/Layouts/default.ctp looks like this:

{% highlight html %}
<!DOCTYPE html>
<html>
<head>
   <?php echo $this->Html->charset(); ?>
   echo $this->Html->css('/bootstrap/css/bootstrap.min');
   echo $this->Html->css('styles');
   ?>
</head>
<body class="<?php echo strtolower($this->name . '-' . $this->action); ?>">
   <div class="container-narrow">
      <!-- … more markup -->
      <?php echo $this->Session->flash(); ?>
      <?php echo $this->fetch('content'); ?>
   </div><!-- .container-narrow -->
</body>
</html>
{% endhighlight %}

The content of the view template is inserted by the call to 

{% highlight php %}
<?php
$this->fetch('content');
{% endhighlight %}

The Session helper outputs to feedback to the user like e.g. a success message when saving a project.  
