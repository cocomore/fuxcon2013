In Symfony, basic CRUD code can be [generated](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/commands/generate_doctrine_crud.html) by the command

{% highlight bash %}
php app/console generate:doctrine:crud --entity="Project"
{% endhighlight %}

We started with this, and added the special handling for tags and pictures for our project. Our ProjectController in file src/FUxCon2013/ProjectsBundle/Controller/ProjectBundle has two helper methods:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Controller;

class ProjectController extends Controller
{
    /**
     * Send flash message
     */
    private function flash($message, $type = 'error')
    { $this->get('session')->getFlashBag()->add($type, $message); }

    /**
     * Redirect to the project detail page
     */
    private function show($project)
    {
        return $this->redirect(
            $this->generateUrl('project_show', array('id' => $project->getId()))
        );
    }
}
{% endhighlight %} 

With these, the updateAction() looks like this:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Controller;

class ProjectController extends Controller
{
      /**
       * Edits an existing Project entity.
       *
       * @Route("/project/{id}", name="project_update")
       * @Method("PUT")
       * @Template("FUxCon2013ProjectsBundle:Project:edit.html.twig")
       *
       * Uses type hint "Project $project" to implicitely invoke ParamConverter
       */
      public function updateAction(Request $request, Project $project)
      {
          if (!$this->get('security.context')->isGranted('MAY_EDIT', $project)) {
              $this->flash('You are not allowed to edit this project');
              return $this->show($project);
          }

          $editForm = $this->createForm(new ProjectType(), $project);
          $editForm->bind($request);

          if ($editForm->isValid()) {
              if (!$project->getPicture() || $project->processPicture()) {
                  $em = $this->getDoctrine()->getManager();
                  $em->persist($project);
                  $em->flush();

                  $this->get('fpn_tag.tag_manager')->saveTagging($project);

                  $this->flash('The project has been saved', 'success');
                  return $this->show($project);
              }
              else {
                  $this->flash('Picture could not be saved. Please try again.');
              }
          }
          else {
              $this->flash('The project could not be saved. Please, try again.');
          }

          return array(
              'project'      => $project,
              'edit_form'   => $editForm->createView(),
          );
      }
  }
}
{% endhighlight %}

This method handles submitted for data. The form itself is displayed by this code:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Controller;

class ProjectController extends Controller
{
  /**
   * Displays a form to edit an existing Project entity.
   *
   * @Route("/project/{id}/edit", name="project_edit")
   * @Method("GET")
   * @Template()
   *
   * Uses type hint "Project $project" to implicitely invoke ParamConverter
   */
  public function editAction(Project $project)
  {
      if (!$this->get('security.context')->isGranted('MAY_EDIT', $project)) {
          $this->flash('You are not allowed to edit this project');
          return $this->show($project);
      }

      $this->get('fpn_tag.tag_manager')->loadTagging($project);

      $editForm = $this->createForm(new ProjectType(), $project);

      return array(
          'project'      => $project,
          'edit_form'   => $editForm->createView(),
      );
  }
}
{% endhighlight %}

The template for this lives in in file src/FUxCon2013/ProjectsBundle/Resources/views/Project/edit.html.twig:

{% highlight html %}
{% raw %}
{% extends 'FUxCon2013ProjectsBundle::layout.html.twig' %}

{% block content -%}
<div class="row">
    <form action="{{ path('project_update', { 'id': project.id }) }}" method="post" {{ form_enctype(edit_form) }} class="span6 offset1">
        <h1>Edit project</h1>

        <input type="hidden" name="_method" value="PUT" />
        {{ form_widget(edit_form) }}
        <p>
            <button class="btn" id="save-button" type="submit">Save</button>
        </p>
    </form>
</div>
{% endblock %}
{% endraw %}
{% endhighlight %}

The form itself is constructed neither here nore in the controller but rather in a separate class in file src/FUxCon2013/ProjectsBundle/Form/ProjectType.php:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Form;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('attr' => array(
                'class' => 'title-field input-block-level'
            )))
            ->add('picture', 'file', array('required' => false))
            ->add('startDate', 'date_entry')
            ->add('endDate', 'date_entry')
            ->add('about', null, array('attr' => array(
                'class' => 'about-field input-block-level'
            )))
            ->add('tags', 'tags_entry', array('attr' => array(
                'class' => 'tags-field input-block-level'
            )))
        ;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FUxCon2013\ProjectsBundle\Entity\Project'
        ));
    }

    public function getName()
    { return 'fuxcon2013_projectsbundle_projecttype'; }
}
{% endhighlight %}

In this form, the fields with there desired front end properties are listed. It is [not possible to set CSS IDs](http://stackoverflow.com/questions/17070763/how-to-set-a-css-id-attribute-to-a-symfony2-form-input) as Symfony uses these for injecting Javascript validation code into the form.

For dates and tags, we have created new widget types *date_entry* and *tags_entry*. The widget type for tags is [described elsewhere]({{ site_url }}/fuxcon2013/tagging/#symfony). The *date_entry* is much simpler, but still requires another class and some configuration. Here is the widget class in 
src/FUxCon2013/ProjectsBundle/Form/DateType.php

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    { $builder->addModelTransformer(new DateTransformer()); }

    public function getParent()
    { return 'text'; }

    public function getName()
    { return 'date_entry'; }
}
{% endhighlight %}

This class needs to be registered in app/config/config.yml for Symfony to recognize it:

{% highlight yaml %}
services:
    fuxcon2013.form.date_entry:
        class: FUxCon2013\ProjectsBundle\Form\DateType
        tags:
            - { name: form.type, alias: date_entry }
{% endhighlight %}
