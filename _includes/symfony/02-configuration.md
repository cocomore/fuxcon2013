Symfony uses multiple locations for its settings:

<table class="table table-bordered table-striped">
  <thead>
    <tr><th style="white-space: nowrap">File</th><th>Major settings</th></tr>
  </thead>
  <tbody>
    <tr>
      <td>composer.json</td>
      <td>Versions of all installed bundles</td>
    </tr>
    <tr>
      <td>app/config/config.yml</td>
      <td>Configuration of all installed bundles and services</td>
    </tr>
    <tr>
      <td>app/config/parameters.yml</td>
      <td>Database credentials</td>
    </tr>
    <tr>
      <td>app/config/security.yml</td>
      <td>Configuration of authentication-related services, 
      path-based access control</td>
    </tr>
    <tr>
      <td>app/Kernel.php</td>
      <td>Registration of all installed bundles</td>
    </tr>
  </tbody>
</table>

Routes can be configured in multiple locations. Symfony mimics Python decorators based on specially formatted comments in the PHP controller files and uses a special preprocessor to extract these so-called annotations to serialized data structures in a cache directory. These annotations allow to specify routes directly at the controllers/actions which is what I use for this project.
