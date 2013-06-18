Our picture handling in Symfony mimics the way we have used in our CakePHP implementation. [There]({{ site.url }}/fuxcon2013/pictures/#cakephp), we have already mentioned that this approach would need some refinements if used on a production site.

Our model has some barebones methods for getting and setting pictures and for moving them to their proper place after update:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Entity;

class Project
{
  
  private function getPicturePath()
  { return __DIR__ . '/../../../../web/images/project/' . $this->getId() . '.jpg'; }

  public function getPicture()
  {
      if (empty($this->picture)) {
          return null;
      }
      return new \Symfony\Component\HttpFoundation\File\File(
          $this->getPicturePath(), /*checkPath*/false
      );
  }

  public function setPicture($picture)
  {
      $mimeType = $picture->getMimeType();
      if (!in_array($mimeType, array('image/jpeg'))) {
          throw Exception("You may only have images of type JPEG");
      }
      $this->picture = $picture;
  }

  public function processPicture()
  {
      if (! ($this->picture instanceof UploadedFile) ) {
          return false;
      }

      $file = $this->getPicturePath();

      $this->picture->move(
          pathinfo($file, PATHINFO_DIRNAME),
          pathinfo($file, PATHINFO_BASENAME)
      );
      return true;
  }
}
{% endhighlight %}

in Symfony, the construct corresponding to a view helper is an extension to the Twig template language. For our thumbnails, we have implemented such an extension. This provides us with a new function that we can then use in templates. Here is an excerpt from src/FUxCon2013/ProjectsBundle/Resources/views/Project/index.html.twig:

{% highlight html %}
{% raw %}
<a class="thumbnail" href="{{ path('project_show', { 'id': project.id } ) }}">
    <img src="{{ thumbnail([ '/images/project/', project.id, '.jpg' ] | join, '200x200') }}">
</a>
{% endraw %}
{% endhighlight %}  

The function definition lives in file src/FUxCon2013/ProjectsBundle/Twig/Extension/FUxCon2013Extension.php. We have [already shown it]({{ site.url }}/fuxcon2013/views/#symfony) when introducing views in Symfony.

This is the code we use for creating a new project and saving the image:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Controller;

class ProjectController
{
  /**
   * Creates a new Project entity.
   *
   * @Route("/", name="project_create")
   * @Method("POST")
   * @Template("FUxCon2013ProjectsBundle:Project:new.html.twig")
   */
  public function createAction(Request $request)
  {
      $project = new Project();
      $form = $this->createForm(new ProjectType(), $project);
      $form->bind($request);

      if ($form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($project);
          $em->flush();

          // Picture processed after saving. We need the project ID
          if ($project->getPicture() && $project->processPicture()) {
              $this->flash('The project has been saved', 'success');
          }
      }
      // some more processing and error handling
  }
}
{% endhighlight %}

