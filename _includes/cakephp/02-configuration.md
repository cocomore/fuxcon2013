All configuration is done as PHP code in the files in directory app/Config. These are the files that we use:

<table class="table table-bordered table-striped">
  <thead>
    <tr><th style="white-space: nowrap">File in app/Config</th><th>Major settings</th></tr>
  </thead>
  <tbody>
    <tr>
      <td>core.php</td>
      <td>
        Basics:
        <ul>
          <li>Encryption seed</li>
          <li>error and logging levels</li>
          <li>cache and session configurations</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td>bootstrap.php</td>
      <td>Registration of extensions</td>
    </tr>
    <tr>
      <td>database.php</td>
      <td>Database type and credentials</td>
    </tr>
    <tr>
      <td>routes.php</td>
      <td>Routes, the connection between URIs and controllers/actions</td>
    </tr>
  </tbody>
</table>

  
