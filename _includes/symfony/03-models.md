## Models

Symfony manages models through one of its supported database backends. The default is to use the doctrine backend. As with Django, you declare your model fields in either XML, YML or as annotations and use a command line tool to generate the tables in your MySQL database, e.g.

{% highlight bash %}
     php app/console doctrine:generate:entities FUxCon2013/ProjectsBundle/Entity/Project
{% endhighlight %}

Doctrine requires the definition of a getter and a setter for each field. I have opted to use magic methods in a super class to avoid this. Here is the project model from the Symfony implementation in src/FUxCon2013/ProjectsBundle/Entity/Project.php:

{% highlight php %}
<?php
class Project extends GenericAccessors
{
    // The form generator chokes on "protected" attributes  
    public $id;
    public $title;
    public $startDate;
    public $endDate;
    public $about;
    public $created;
    public $modified;

    public $user;
}
{% endhighlight %}

The super class makes sure that with this arrangement the code to be written keeps small while there still are getters and setters for each field, e.g. 

{% highlight php %}
<?php
     $p = new Project();
     echo $p->getTitle();
{% endhighlight %}

Unfortunately, the form generator we want to use for the edit form does some checking on the access level of attributes directly and does not seem to see the getters inherited from the super class. We cannot declare attributes protected because of this.  

In addition, doctrine needs some more information for each field which I provide in a YAMl file, ie.

{% highlight yaml %}
FUxCon2013\ProjectsBundle\Entity\Project:
    type: entity
    table: projects
    id:
        id:
            type: integer
            generator: { strategy: AUTO }
    fields:
        title:
            type: string
            length: 255
        startDate:
            type: date
            column: start_date
        endDate:
            type: date
            column: end_date
        about:

            type: text
        created:
            type: datetime
        modified:
            type: datetime


     manyToOne:
        user:
            targetEntity: User
{% endhighlight %}

Symfony requires a repository class for each model to be able to access collections of model objects. In our implementation, this class contains methods needed for paginated display of projects:

{% highlight php %}
<?php
class ProjectRepository extends EntityRepository
{
    public function count() {
        return $this->getEntityManager()
            ->createQuery('
                SELECT p.title, p.about
                FROM FUxCon2013SitesBundle:Project p'
            )
            ->getResult();
    }

    public function findPaginated($offset, $size) {
        return $this->getEntityManager()
            ->createQuery('
                SELECT p.title, p.about
                FROM FUxCon2013SitesBundle:Project p
                ORDER BY p.title ASC'
            )
            ->setFirstResult($offset)
            ->setMaxResults($size)
            ->getResult();
    }
}
{% endhighlight %}
