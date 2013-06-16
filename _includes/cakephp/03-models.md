In the Model-View-Controller pattern, Models define the types of objects in the problem domain. They also provide functionality to persist such objects in a relational database. 

In CakePHP, models are implemented by PHP classes in the folder app/Model. Plugins can have there own models which then live in app/Plugin/<em>plugin</em>/Model. Model classes represent individual domain objects. They also allow to mainipulate collections of such objects. This can be through a set of powerful find methods or through own custom methods.

Model files generally don't declare scalar database fields. CakePHP reads these from the database and caches them in a serialized structure in app/tmp/cache/models. However, models declare rules for field validation and one of the supported relationship types (1:1, 1:n, n:m). Models also declare special behavior like taggable or tree. a first approximation of models can be generated automatically by the command line tool cake, e.g.

Our implementation uses two models in directory app/Model:

* Project.php - projects listed on the start page
* User.php - user accounts as owners of projects

In CakePHP, models can be enhanced by behaviors. There exist some standard behaviors like Containable, or Tree. Our implementation uses a contributed behavior Taggable to implement the tagging feature. 
