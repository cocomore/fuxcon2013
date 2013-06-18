We use *fpn/tag-bundle* and *fpn/doctrine-extensions-taggable* to implement tagging in our Symfony implementation. The [documentation page](https://github.com/FabienPennequin/FPNTagBundle) goes to some length at explaining what you need in terms of configuration and extending your model classes to enjoy tagging. 

As we want to use YAML configuration for our Doctrine entities and want to actually enter tags in the edit forms of our projects, we had to do some additional leg work to get the bundle to work. 

Our *project* model needs a setter for tags:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Entity;

use DoctrineExtensions\Taggable\Taggable;
use Doctrine\Common\Collections\ArrayCollection;
// more use clauses

/**
 * Project
 */
class Project implements Taggable
{
  public function getTags()
  {
      $this->tags = $this->tags ?: new ArrayCollection();
      return $this->tags;
  }

  public function getTaggableType()
  { return 'project'; }

  public function getTaggableId()
  { return $this->getId(); }

  // We added this to be able to save tags from a form  
  public function setTags($tags)
  {
      $this->tags = is_array($tags) ? new ArrayCollection($tags) : $tags;
  }
}
{% endhighlight %}

Our Doctrine configuration files Tag.orm.yml and Tagging.orm.yml in src/FUxCon2013/ProjectsBundle/Resources/config/doctrine look like this:

{% highlight yaml %}
FUxCon2013\ProjectsBundle\Entity\Tag:
    type: entity
    table: tag
    id:
            id:
                type: integer
                generator:
                    strategy: AUTO
    oneToMany:
        tagging:
            targetEntity: Tagging
            mappedBy: tag
            fetch: EAGER
{% endhighlight %}

{% highlight yaml %}
FUxCon2013\ProjectsBundle\Entity\Tagging:
    type: entity
    table: tagging
    id:
            id:
                type: integer
                generator:
                    strategy: AUTO
    manyToOne:
        tag:
            targetEntity: Tag
{% endhighlight %}

The bundle documentation only explains how to read tags. We want to edit them as well. This requires several extensions. The createAction() and  in src/FUxCon2013/ProjectsBundle/Controller/ProjectController.php need to actively save the tags:

{% highlight php %}
<?php
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

        $this->get('fpn_tag.tag_manager')->saveTagging($project);
        // more work to save pictures
    }
    // error handling done here
}
{% endhighlight %}

The same statement needs to be added to editAction() and updateAction().

To actually be able to edit tags in a form, we need to add them to our edit form. We created a new widget type that converts between comma-separated list and list of tags. First the form in src/FUxCon2013/ProjectsBundle/Form/ProjectType.php:
  
{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Form;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tags', 'tags_entry', array('attr' => array(
                'class' => 'tags-field input-block-level'
            )))
        ;
    }
}
{% endhighlight %}

The new widget type *tags_entry* is created in its own class in src/FUxCon2013/ProjectsBundle/Form/TagsType.php:

{% highlight php %}
<?php
namespace FUxCon2013\ProjectsBundle\Form;

class TagsType extends AbstractType
{
    public function __construct(TagManager $tagManager)
    { $this->tagManager = $tagManager; }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new TagsTransformer($this->tagManager);
        $builder->addModelTransformer($transformer);
    }

    public function getParent()
    { return 'text'; }

    public function getName()
    { return 'tags_entry'; }
}
{% endhighlight %}

This class needs to be registered in app/config/config.yml to be recognized by Symfony and to have it pass a tag manager as parameter:

{% highlight yaml %}
services:
    fuxcon2013.form.tags_entry:
        class: FUxCon2013\ProjectsBundle\Form\TagsType
        arguments: [ "@fpn_tag.tag_manager" ]
        tags:
            - { name: form.type, alias: tags_entry }
{% endhighlight %}

Actually, the class TagsType doesn't do do any of the actual work. The conversion between text entry field and lists of tags in turn is handled by yet another class, a TagsTransformer in file src/FUxCon2013/ProjectsBundle/Form/TagsTransformer.php:
  
{% highlight php %}
<?php
/**
 * @see http://symfony.com/doc/current/cookbook/form/data_transformers.html
 */
namespace FUxCon2013\ProjectsBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{
    private $tagManager;

    public function __construct($tagManager)
    { $this->tagManager = $tagManager; }

    public function transform($tags)
    { return join(', ', $tags->toArray()); }

    public function reverseTransform($tags)
    {
        return $this->tagManager->loadOrCreateTags(
            $this->tagManager->splitTagNames($tags)
        );
    }
}
{% endhighlight %}

The TagsTransformer finally uses some API functions of the tag manager to load or create tags entered by a user.
