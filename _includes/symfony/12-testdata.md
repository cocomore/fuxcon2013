Our Symfony implementation currently does not have any provision for test data generation. The roll-it-yourself approach from our CakePHP implementation is probably ported easily enough. 

There are also some solutions that have a more sophisticated approach which could be applied, namely (gleaned from an answer on [Stack Overflow](http://stackoverflow.com/questions/15159043/test-data-generator-for-symfony2-with-doctrine2)):

* [Doctrine data fixtures](https://github.com/doctrine/data-fixtures)
* [Alice](https://github.com/nelmio/alice)
* [Faker](https://github.com/fzaninotto/Faker)

Some more work is needed to integrate either of these into automated Behat-driven tests. 