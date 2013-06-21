To generate test data from our scenario step definitions, the CakePHP implementation provides a custom, ancillary method createProjects() in controller BehatController in file app/Controller/BehatController.php. In the step definition, we call this before running a test suite to initialize the test data:

{% highlight php %}
<?php
class FeatureContext extends MinkContext
{
  const PROJECT_COUNT = 7;

  /**
   * @BeforeSuite
   */
  public static function prepare(SuiteEvent $event)
  {
    $params = $event->getContextParameters();
    $baseUrl = $params['base_url'];

    /* 
     * Initialize test database with more projects than 
     * what fits on a page
     */
    $config = json_decode(file_get_contents($baseUrl . 'behat/config'));
    if (!$config) {
      throw new Exception('Could not get config from ' . $baseUrl);
    }
    $count = $config->pageSize * 3 + intval($config->pageSize / 2);
    $result = json_decode(file_get_contents($baseUrl . 'behat/createProjects/' . self::PROJECT_COUNT));

    if ($result->status != "OK") {
      throw new Exception($result->message);
    }
  }
}
{% endhighlight %}

On the server side, there was some ugly fiddling required to make the creation of demo picture uploads work.

{% highlight php %}
<?php
class BehatController {
  function createProjects($count = 0)
  {
    $tmp = tempnam('/tmp', 'testimage.');
    $picture = array(
      'tmp_name' => $tmp,
      'type' => 'image/jpeg',
      'error' => 'behat_test',
    );

    for ($i = 1; $i <= $count; $i++) {
      copy(IMAGES . 'testimage.jpg', $tmp);

      $this->Project->create();
      if (!$this->Project->save(array(
        'picture' => $picture,
      ))) {
        throw new Exception("SAVE failed");
      }
    }
  }
}
{% endhighlight %}

In the Project model, this needs to be a special case as in this case the image is not an uploaded one:

{% highlight php %}
<?php
class Project extends AppModel {

  function beforeSave($options = array()) {
    $file = $this->data[$this->alias]['picture'];

    if ($file['error'] === UPLOAD_ERR_NO_FILE 
      || $file['error'] === 'behat_test') {
      return TRUE;
    }
  }
  
  function afterSave($created) {
    $file = $this->data[$this->alias]['picture'];
    if ($file['error'] === 'behat_test') {
      if (!rename($file['tmp_name'], IMAGES . 'project' . DS . $this->id . '.jpg')) {
        throw new Exception("Failed to move file: " 
          . posix_strerror(posix_get_last_error()));
      }
    }
    else
    if ($file['error'] === UPLOAD_ERR_OK) {
      if (!move_uploaded_file($file['tmp_name'], IMAGES . 'project' . DS . $this->id . '.jpg')) {
        throw new Exception("Failed to move file: " 
          . posix_strerror(posix_get_last_error()));
      }
    }
  }
}
{% endhighlight %} 
