Controllers in Symfony are classes in the bundle of your application. In our example, this is the file src/FUxCon2013/ProjectsBundle/Controller/ProjectsController.php. Here is an excerpt:

{% highlight php %}
<?php
class ProjectsController extends Controller
{
  const NO_COL = 3;
  const PAGE_SIZE = 5;

  /**
   * @Route("/", defaults={"offset" = 1})
   * @Route("/page:{offset}", name="_projects")
   * @Template()
   */
  function indexAction(Request $request, $offset = 1)
  {
    $repo = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FUxCon2013ProjectsBundle:Project');

    $limit = 10;
    $from  = (($offset * $limit) - $limit);

    $totalCount = $repo->count();
    $totalPages = ceil($totalCount / $limit);

    $projects = $repo->findPaginated($from, $limit);

    $columns = array();
    foreach ($projects as $i => $project) {
        $col = $i % self::NO_COL;
        $columns[$col][] = $project;
    }

    $vars = array(
        'columns' => $columns,
        'width' => 12 / self::NO_COL,
        'page' => $offset,
        'totalPages' => $totalPages,
        'body_class' => 'projects-index',
    );

    return $vars;
  }

  // … more action methods
}
{% endhighlight %}

We use the option to declare routes as annotations in comments. In this example, the method ProjectsController.indexAction() is accessed through the routes    

{% highlight php %}
<?php
/**
 * @Route("/", defaults={"offset" = 1, "tag" = null})
 * @Route("/page:{offset}", name="_projects")
 */
{% endhighlight %}

Symfony does not have pagination built in so we use our custom methods from the repository to get at the count and the paginated list of projects. 

The annotation @Template() signals to Symfony that we want the variables returned from the controller to be rendered by a template with a standard name, in our case the file src/FUxCon2013/ProjectBundle/Resources/views/Projects/index.html.twig. We show this file below when explaining the views.

### Parameter converters

A powerful concept related to controllers are [parameter converters](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html). Initially, parameters passed to actions are strings derived from an associated route. In many cases however, they denote domain objects. The process of finding a domain object from an URL parameter can be delegated out if the controller by using a dedicated parameter converter. We make use of this mechanism in our controller. Here is the declaration of our ProjectController.showAction() to display a single project:
  
{% highlight php %}
<?php
/**
 * Finds and displays a Project entity.
 *
 * @Route("/project/{id}", name="project_show")
 * @Method("GET")
 * @Template()
 *
 * Uses type hint "Project $project" to implicitely invoke ParamConverter
 */
public function showAction(Project $project)
{}
{% endhighlight %}
  
Symfony is even smart enough to derive the need for a parameter converter from the type hint given to the parameter. Even more so, there is a built-in <em>doctrine.orm</em> converter that fetches an object from the database based on its primary key. So, in our simple example, Symfony can get our project completely on its own. If a matching project is not found, a 404 response is generated.
