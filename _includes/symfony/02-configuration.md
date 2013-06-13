## Configuration

Symfony uses multiple locations for its settings:

File | Major settings 
-----|---------------
composer.json | Versions of all installed bundles
app/config/config.yml | Configuration of all installed bundles and services app/config/parameters.yml | Database credentials
app/config/security.yml | Configuration of authentication-related services, path-based access control
app/Kernel.php | Registration of all installed bundles

Routes can be configured in multiple locations. Symfony mimics Python decorators based on specially formatted comments in the PHP controller files and uses a special preprocessor to extract these so-called annotations to serialized data structures in a cache directory. These annotations allow to specify routes directly at the controllers/actions which is what I use for this project.
