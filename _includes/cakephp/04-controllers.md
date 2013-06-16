CakePHP uses entries in the file app/Config/routes.php to map request URIs to Controller methods. 

Here is the route declared for our projects in app/Config/routes.php:

{% highlight php %}
<?php
Router::connect('/', array('controller' => 'projects', 'action' => 'index'));  
{% endhighlight %}

This declares the homepage to be processed by controller method ProjectsController.index(). We rely on the default format for routes, e.g. /projects/view/32 for the other routes in our implementation. 

Controllers are classes located in a file in app/Controller. Here is part of our ProjectsController.php:

{% highlight php %}
<?php
class ProjectsController extends AppController {

  const NO_COL = 3;
  const PAGE_SIZE = 5;

  public $paginate = array(
    'Project' => array(
      'limit' => self::PAGE_SIZE,
    )
  );
 
  public $helpers = array('Thumbnail', 'Markdown.Markdown');

  public function index() {
    $this->Project->recursive = 0;
    $this->set('title_for_layout', 'Projects');


    $projects = $this->paginate('Project');
    $columns = array();
    foreach ($projects as $i => $project) {
      $col = $i % self::NO_COL;
      $columns[$col][] = $project;
    }
    $this->set(array(
      'columns' => $columns,
      'width' => 12 / self::NO_COL,
    ));
  }
  // … more actions
}
{% endhighlight %}

To make our example a bit more interesting, this code gets a paginated list of projects from the database and arranges this into columns. The controller uses $this->set() to pass variables to its associated view (see below) which then renders it to the client browser. Pagination is a built-in feature that gets configured by an instance variable $paginate and uses the method paginate() to get a page worth of project objects.

Controllers declare certain aspects of their operation as instance variables. Configuration shared with other controllers can be inherited from a common base class AppController. For example, our ProjectsController inherits the design appearance, and components needed for authentication from this base class:

{% highlight php %}
<?php
class AppController extends Controller {
  public $theme = 'Bootstrap';
  public $components = array(
    'DebugKit.Toolbar',
    'Session', 
    'Auth' => array(
      'loginRedirect' => array('controller' => 'projects', 'action' => 'index'),
      'logoutRedirect' => array('controller' => 'projects', 'action' => 'index'),
      'authorize' => array('Controller'),
      'element' => 'message-auth',
    )
  );

  public function beforeFilter() {
    $this->Auth->allow('index', 'view');
  }
  // … more common methods
}
{% endhighlight %}
