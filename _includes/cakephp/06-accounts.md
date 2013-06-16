CakePHP provides a flexible Auth component that does most of the work required for managing user accounts. It is declared in the controller or the common base class AppController (excerpt from file app/Controller/AppController.php):

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

  public function isAuthorized($user) {
    // Admin can access every action
    if (isset($user['role']) && $user['role'] === 'admin') {
      return TRUE;
    }

    // Default deny
    return FALSE;
  }
}
{% endhighlight %}

Controllers overwrite allowed access by overwriting the isAuthorized() method:

{% highlight php %}
<?php
class ProjectsController
{
   public function isAuthorized($user, $action = NULL) {
    // All registered users can add projects
    if ($this->action === 'add') {
      return true;
    }

    if (!$action) {
      $action = $this->action;
    }

    // The owner of a project can edit and delete it
    if (in_array($action, array('edit', 'delete'))) {
      $projectId = $this->request->params['pass'][0];
      if ($this->Project->isOwnedBy($projectId, $user['id'])) {
        return TRUE;
      }
    }
    return parent::isAuthorized($user);
  }
{% endhighlight %}

Apart from this simple, form-based authentication with controller-based authorization, other variants are possible as [documented in the Cake Book](http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html).
