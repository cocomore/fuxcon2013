Django refers to itself not as a Model-View-Controller framework, but rather as a Model-View-Template framework, i.e. views take the role of controllers and templates that of views in the other frameworks.

Our simple project consists only of a single app (the contents of the projects directory). Generally though, Django projects consist of multiple apps, each with its own set of routes, models, views, templates.

Once a new project is created with the common line tool manage.py, Django uses a single file (projects/settings.py in our case) to configure most of its components and functionality. Routes are kept in a different file though. Optionally, different hosts can be configured for a multi-domain setup. We don't use this feature, though.

<table class="table table-bordered table-striped">
  <thead>
    <tr><th style="white-space: nowrap">File in projects</th><th>Major settings</th></tr>
  </thead>
  <tbody>
    <tr>
      <td>settings.py</td>
      <td>
        Basics:
        <ul>
          <li>Encryption seed</li>
          <li>Error and logging levels</li>
          <li>Path names</li>
          <li>Database credentials</li>
          <li>Installed apps and middlewares</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td>urls.py</td>
      <td>Routes for our app, mapping regular expressions to views</td>
    </tr>
    <tr>
      <td>admin.py</td>
      <td>Settings for the admin interface automatically generated from the models</td>
    </tr>
  </tbody>
</table>
