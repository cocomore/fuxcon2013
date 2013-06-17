In Symfony, to implement permissions, there are basically two approaches. Access control lists (ACLs) and voters. [Access control lists](http://symfony.com/doc/master/cookbook/security/acl.html) store additional information about individual Access Control Entities (ACEs) and associated information in the database. 

In situations where permissions can be derived from existing relationships between domain objects, this overhead can be avoided by implementing a [voter](http://symfony.com/doc/current/cookbook/security/voters.html).

Our implementation contains such a voter. With the voter, two things are possible:

* Hide an edit link from the project detail page
* Prevent unauthorized users from accessing the project edit page. 

In case of the edit link, this is the responsible code in our template src/FUxCon2013/ProjectBundle/Resources/views/Project/show.html.twig:

{% highlight html %}
{% raw %}
<p class="actions">
   {% if is_granted("MAY_EDIT", project) %}
     <a class="btn" id="edit-project" href="{{ path('project_edit', { 'id': project.id }) }}">edit this project</a>
   {% endif %}
 </p>
{% endraw %}
{% endhighlight %}

This is the check in our method ProjectController.editAction():

{% highlight php %}
<?php
if (!$this->get('security.context')->isGranted('MAY_EDIT', $project)) {
    $this->flash('You are not allowed to edit this project');
    return $this->show($project);
}
{% endhighlight %}

In both cases, a method is_granted() rsp. isGranted() is called with a role and the project object. Under the hood, a voter in file FUxCon2013/ProjectsBundle/Security/OwnerVoter.php does the checking:

{% highlight php %}
<?php
function vote(TokenInterface $token, $object, array $attributes)
{
    if (!in_array('MAY_EDIT', $attributes)) {
        return self::ACCESS_ABSTAIN;
    }
    if (!($object instanceof Project)) {
        return self::ACCESS_ABSTAIN;
    }

    $user = $token->getUser();
    $securityContext = $this->container->get('security.context');

    return $securityContext->isGranted('IS_AUTHENTICATED_FULLY')
        && $user->getId() == $object->getUser()->getId()
        ? self::ACCESS_GRANTED
        : self::ACCESS_DENIED;
}
{% endhighlight %}

To make this work, some configuration is required. In file app/config/config.yml, we register the voter as a service:

{% highlight yaml %}
services:
    fuxcon2013.security.owner_voter:
        class:      FUxCon2013\ProjectsBundle\Security\OwnerVoter
        public:     false
        arguments: [ @service_container ]
        tags:
            - { name: security.voter }
{% endhighlight %}

In app/config/security.yml, we have to set the strategy of the access decision manager and tell it to allow access if all voters abstain:

{% highlight yaml %}
security:
    access_control:
        - { path: ^/project/\d+/edit, role: MAY_EDIT }

    access_decision_manager:
        # strategy can be: affirmative, unanimous or consensus
        strategy: unanimous
        allow_if_all_abstain: true
{% endhighlight %}
