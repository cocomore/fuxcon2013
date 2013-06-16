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
