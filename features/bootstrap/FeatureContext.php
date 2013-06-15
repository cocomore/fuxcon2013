<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Event\SuiteEvent;

/**
 * Features context.
 */
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

  private function startSessionWithPage($path = '/')
  {
    $baseUrl = $this->getMinkParameter('base_url');
    $session = $this->getSession('goutte');
    $session->start();
    
    // Make sure we don't have a double '/' in path
    //D echo "BASE=", $baseUrl, ", PATH=", $path, "\n";
    //D echo "BLA1=", substr($baseUrl, -1, 1), ", BLA2=", substr($path, 0, 1), "\n";
    if (substr($baseUrl, -1, 1) == '/' && substr($path, 0, 1) == '/') {
      $url = $baseUrl . substr($path, 1);
    }
    else {
      $url = $baseUrl . $path;
    }
    $session->visit($url);
    return $session;
  }

 /**
   * @Given /^each project listed shows title, picture, and text$/
   *
   * In a loop over all .project elements, validate that each contains
   * title, thumbnail picture and some teaser text. 
   */
  public function eachProjectListedShowsTitlePictureAndText()
  {
    $page = $this->startSessionWithPage('/')->getPage();
    foreach ($page->findAll('css', '.project') as $i => $project) {
      $title = $project->find('css', 'h4'); 
      if (!$title || strlen($title->getText()) < 10) {
        throw new Exception('Project #' . $i + 1 . ' has no title');
      }

      $picture = $project->find('css', 'a.thumbnail img');
      if (!$picture 
        || strpos($picture->getAttribute('src'), '/img/thumbnails/') !== 0) {
        
        throw new Exception('Project #' . $i + 1 . ' has no picture');
      }
      
      $text = $project->find('css', 'p.about');
      if (!$text || strlen($text->getText()) < 30) {
        throw new Exception('Project #' . $i + 1 . ' has no text');
      }
    }
  }

  /**
   * @Given /^each title is linked to the project detail page$/
   */
  public function eachTitleIsLinkedToTheProjectDetailPage()
  {
    $page = $this->startSessionWithPage('/')->getPage();
    foreach ($page->findAll('css', '.project h4 a') as $i => $link) {
      $href = $link->getAttribute('href');
      if (!$href || strpos($href, '/projects/view/') !== 0) {
        throw new Exception('Project #' . $i + 1 . ' has no link');
      }

    }
  }

  /**
   * @Given /^each picture is a valid, (\d+)px wide "(\w+)" thumbnail image$/
   */
  public function eachPictureIsValidThumbnailImage($width, $ext)
  {
    $baseUrl = $this->getMinkParameter('base_url');
    $tmp = tempnam('/tmp', 'thumbnail.');

    $page = $this->startSessionWithPage('/')->getPage();
    foreach ($page->findAll('css', '.project a.thumbnail img') as $i => $link) {
      $pictureSrc = $link->getAttribute('src');
      
      if (strpos($pictureSrc, '/img/thumbnails/') !== 0) {
        throw new Exception($pictureSrc . ' is not a thumbnail');
      }
      $pictureExt = pathinfo($pictureSrc, PATHINFO_EXTENSION);
      if ($pictureExt != $ext) {
        throw new Exception($pictureSrc . ' has extension ' 
          . $pictureExt . '. Should be ' . $ext);
      }
      
      file_put_contents($tmp, file_get_contents($baseUrl . $pictureSrc));
      list($pictureWidth) = getimagesize($tmp);
      
      if ($pictureWidth != $width) {
        throw new Exception($pictureSrc . ' is ' 
          . $pictureWidth . 'px wide. Should be ' . $width . 'px.');
      }
    }
  }
  
  /**
   * @Given /^I am logged out$/
   */
  public function iAmLoggedOut()
  {
    try {
      $this->iAmLoggedIn();      
    }
    catch (Exception $e) {
      if ($e->getMessage() != 'Not logged in') {
        throw $e;
      }
    }
  }
  
  /**
   * @Given /^I am logged in$/
   */
  public function iAmLoggedIn()
  {
    $session = $this->getSession('goutte');
    $session->visit('/behat/checkLogin');
    $status = json_decode($session->getPage()->getText());
    if (!$status) {
      throw new Exception('Could not get status');
    }
    if (!$status->logged_in) {
      echo "STATUS:\n"; var_dump($status);
      throw new Exception("Not logged in");
    }
  }

  /**
   * @When /^I logout$/
   */
  public function iLogout()
  {
    $this->startSessionWithPage('/users/logout');
    
    try {
      $this->iAmLoggedOut();
    }
    catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * @When /^I login$/
   */
  public function iLogin()
  {
    $this->startSessionWithPage('/users/login');
    $this->iFillInFieldsAndSubmitTheForm('username, password', 'login-button');

    /*
    $page = $this->getSessionWithPage('/users/login')->getPage();
    $page->fillField('username-field', 'tester');
    $page->fillField('password-field', 'tester');
    $page->pressButton('login-button');
    */
    
    try {
      $this->iAmLoggedIn();
    }
    catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * @Then /^there is no link on the page to create a project$/
   */
  public function thereIsNoLinkOnThePageToCreateAProject()
  {
    $page = $this->getSession('goutte')->getPage();
    if ($page->has('css', 'a[href="/users/login"]')) {
      throw new Exception("There is still a login link");
    }
  }

  /**
   * @Then /^I am allowed to access ([\w\/]+)$/
   */
  public function iAmAllowedToAccessPath($path)
  {
    $session = $this->getSession('goutte');
    $session->visit($path);

    // Check for redirect to login page
    if (parse_url($session->getCurrentUrl(), PHP_URL_PATH) == '/users/login') {
      throw new Exception("Not allowed to access {$path}");
    }
  }

  /**
   * @Then /^I am not allowed to access ([\w\/]+)$/
   */
  public function iAmNotAllowedToAccessPath($path)
  {
    $session = $this->getSession('goutte');
    $session->visit($path);

    if (parse_url($session->getCurrentUrl(), PHP_URL_PATH) != '/users/login') {
      throw new Exception("Allowed to access {$path}");
    }
  }

  /**
   * @Given /^I am presented with a webform with ((exactly )?)\(([^\)]+)\)$/
   */
  public function iAmPresentedWithAWebformWithFields($exactly, $fields)
  {
    $page = $this->getSession('goutte')->getPage();
    
    /*
     * Convert comma-separated list of fields
     * into an array with field names as keys and values as FALSE
     */
    $fields = array_map(
      function() { return FALSE; },
      array_flip(preg_split('/,\s*/', $fields))
    );

    foreach ($page->findAll('css', 'form input') as $i => $element) {
      $name = $element->getAttribute('name');
      if ($exactly && isset($fields[$name])) {
        throw new Exception("Additional field \"{$name}\" in form");
      }
      $fields[$name] = TRUE;
    }

    foreach ($fields as $field => $found) {
      if (!$found) {
        throw new Exception("Field \"{$field}\" missing in form");
      }
    }
  }

  /**
   * Provide values for /users/add form
   */
  public function values_users_add(&$values) {
    $now = strftime('%Y%m%d%H%M%S');
    $values['username'] = 'u' . $now;
    $values['password'] = 'p' . $now;
  }

  /**
   * Provide values for /users/login form
   */
  public function values_users_login(&$values) {
    $values['username'] = 'tester';
    $values['password'] = 'tester';
    error_log("BEFORE LOGIN\n", 3, '/tmp/behat.log');
  }
  
  /**
   * Provide values for /projects/add form
   */
  public function values_projects_add(&$values) {
    $key = uniqid();
    $start_month = 6;
    $end_month = 4;

    $values['title'] = "Sample project #{$key}";
    $values['about'] = "Lorem #{$key} ipsum dolor sit amet, consetetur
sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et
dolore magna aliquyam erat, sed diam voluptua.

At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd
gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem
ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
tempor invidunt ut labore et dolore magna aliquyam erat, sed diam
voluptua.

At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd
gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.";
    $values['start_date'] = strftime('%Y-%m-%d',
      strtotime("{$start_month} months ago"));
    $values['end_date'] = strftime('%Y-%m-%d',
      strtotime("{$end_month} months ago"));
    $values['tags'] = 'aaa, bbb, ccc';

    $picture = tempnam('/tmp', 'testimage.') . '.jpg';
    copy(dirname(__FILE__) . '/../../cakephp/app/webroot/img/testimage.jpg', $picture);
    $values['picture'] = $picture;
  }

  /**
   * @Given /^I fill in \(([^\)]+)\) and submit the form( via "([^"]*)")?$/
   */
  public function iFillInFieldsAndSubmitTheForm($fields, $submitButton = ' via "save-button"')
  {
    $values = array();
    
    $session = $this->getSession('goutte');
    
    $method = 'values' 
      . preg_replace('/\W+/', '_', parse_url($session->getCurrentUrl(), PHP_URL_PATH));
    //D echo "METHOD {$method}\n";
    if (method_exists($this, $method)) {
      //D echo "INVOKE {$method}\n";
      $this->{$method}($values);
    }
    
    $page = $session->getPage();

    $fields = preg_split('/,\s*/', $fields);
    foreach ($fields as $field) {
      if (isset($values[$field])) {
        $page->fillField($field . '-field', $values[$field]);
      }
    }
    
    if (strpos($submitButton, ' via ') === 0) {
      preg_match('/\s*via "([^"]*)"/', $submitButton, $matches);
      $submitButton = $matches[1];
    }
    $page->pressButton($submitButton);
  }

  /**
   * @Then /^a new project is created$/
   */
  public function aNewProjectIsCreated()
  {
    $session = $this->getSession('goutte');
    $url = $session->getCurrentUrl();
    if (!preg_match('#/projects/view/(\d+)$#', $url, $matches)) {
      throw new Exception("Not on project view page");
    }
    echo "Project ID: {$matches[1]}\n";
    
    if (empty($matches[1])) {
      throw new Exception("No project created");
    }
  }
  
  /*
   * @group userregistration
   */

   /**
    * @Given /^I am at "([^"]*)"$/
    */
   public function iAmAt($path)
   {
     $session = $this->getSession('goutte');
     $session->visit($path);
     if ($path == $session->getCurrentUrl()) {
       throw new Exception("Not at {$path}");
     }
   }
   
   /**
    * @When /^I click on registration link$/
    */
   public function iClickOnRegistrationLink()
   {
     $page = $this->getSession('goutte')->getPage();
     $link = $page->find('css', '.register');
     if (!$link) {
       throw new Exception("No registration link");
     }
     $link->click();
   }

   /**
    * @Given /^I am redirected to the homepage with a success message$/
    */
   public function iAmRedirectedToTheHomepageWithASuccessMessage()
   {
     $page = $this->getSession('goutte')->getPage();
     $message = $page->find('css', '.alert-success')->getText();
     preg_match('/The user "([^"]*)" has been saved/', $message, $matches);
     echo "User {$matches[1]} saved\n";
   }

   /**
    * @Then /^I am redirected to the homepage with an error message$/
    */
   public function iAmRedirectedToTheHomepageWithAnErrorMessage()
   {
     $session = $this->getSession('goutte');
     $path = parse_url($session->getCurrentUrl(), PHP_URL_PATH);
     echo "URL {$path}\n";
     if ('/' != $path) {
       throw new Exception("Not on homepage, but on {$path}");
     }

     $page = $session->getPage();
     $message = $page->find('css', '.alert-error')->getText();
     echo "ERROR {$message}\n";
   }

   /**
    * @When /^I click on login link$/
    */
   public function iClickOnLoginLink()
   {
     $session = $this->getSession('goutte');
     $session->visit('/users/login');
     if (($url = parse_url($session->getCurrentUrl(), PHP_URL_PATH)) != '/users/login') {
       throw new Exception("Not on login page, but on {$url}");
     }
   }

   /**
    * @When /^I click on logout link$/
    */
   public function iClickOnLogoutLink()
   {
     $session = $this->getSession('goutte');
     $session->visit('/users/logout');
     $page = $session->getPage();
     $el = $page->find('css', '.alert-success');
     if (!$el) {
       throw new Exception('No goodby message');
     }
     $message = $el->getText();
     if (stripos($message, 'logged out') === FALSE) {
       throw new Exception('Goodby message empty');
     }
   }
   
   /**
    * @Given /^I get a welcome message$/
    */
  public function iGetAWelcomeMessage()
  {
    $page = $this->getSession('goutte')->getPage();
    $el = $page->find('css', '.alert-success');
    if (!$el) {
      throw new Exception('No welcome message');
    }
    $message = $el->getText();
    if (stripos($message, 'welcome') === FALSE) {
      throw new Exception('No welcome message');
    }
  }

  /*
   * @group projectdetail
   */

  /**
   * @When /^I click on the first project title$/
   */
  public function iClickOnTheFirstProjectTitle()
  {
    $session = $this->getSession('goutte');
    $this->visit('/');
    $page = $session->getPage();
    $page->find('css', '.project h4 a')->click();
  }

  /**
   * @Then /^I am on a project detail page$/
   */
  public function iAmOnAProjectDetailPage()
  {
    $session = $this->getSession('goutte');
    $url = $session->getCurrentUrl();
    if (!preg_match('#/projects/view/(\d+)$#', $url, $matches)) {
      throw new Exception("Not on project view page");
    } 
  }

  /**
   * @Then /^I see content \(([^\)]+)\)$/
   */
  public function iSeeContent($fields)
  {
    $page = $this->getSession('goutte')->getPage();
    $fields = preg_split('/,\s*/', $fields);
    foreach ($fields as $field) {
      if (!($el = $page->find('css', ".{$field}-content"))) {
        throw new Exception("No content for \"{$field}\"");
      }
      $text = trim($el->getHtml());
      if (empty($text)) {
        throw new Exception("Content for \"{$field}\" is empty");
      }
    }
  }
  
  /**
   * @Given /^the picture has a width of (\d+)px$/
   */
  public function thePictureHasAWidthOf($width)
  {
    $page = $this->getSession('goutte')->getPage();
    if (!($img = $page->find('css', ".picture-content img"))) {
      throw new Exception("No picture on page");
    }
    $url = $img->getAttribute('src');
    if (strpos($url, 'http') !== 0) {
      $baseUrl = $this->getMinkParameter('base_url');
      $url = $baseUrl . $url;
    }
    $size = getimagesize($url);
    if ($width != $size[0]) {
      throw new Exception("Picture has wrong width \"{$size[0]}px\"");
    }
  }

  /**
   * @Given /^the "([^"]*)" content is formatted with Markdown$/
   */
  public function theContentIsFormattedWithMarkdown($field)
  {
    $page = $this->getSession('goutte')->getPage();
    if (!($content = $page->find('css', ".{$field}-content"))) {
      throw new Exception("No \"{$field}\" content on page");
    }
    
    // Heuristic: Markdown reformats \n\n as adjacent paragraphs
    if (strpos($content->getHtml(), "\n\n") !== FALSE) {
      throw new Exception("Content in \"{$field}\" does not look Markdown formatted");
    }
  }
  
  /*
   * @group projectedit
   */

  /**
  * @Given /^I am on the detail page of my first project$/
  */
  public function iAmOnTheDetailPageOfMyFirstProject()
  {
    $this->iLogin();
    $session = $this->getSession('goutte');
    $session->visit('/behat/myFirstProject');
    $project = json_decode($session->getPage()->getText());
    $session->visit("/projects/view/{$project->project_id}");
    echo "user={$project->user_id}, project={$project->project_id}\n";
  }

  /**
  * @Then /^there is a button to edit the project$/
  */
  public function thereIsAButtonToEditTheProject()
  {
    $session = $this->getSession('goutte');
    $url = $session->getCurrentUrl();
    echo "URL={$url}\n";
    if (!preg_match('#/projects/view/(\d+)$#', $url, $matches)) {
      throw new Exception("Not on project view page");
    }

    // Save for next step
    $this->_project_id = $matches[1];

    $page = $session->getPage();
    if (!$page->findById("edit-project")) {
      echo $page->getHtml(); exit;
      throw new Exception("No edit button on page");
    }
  }

  /**
  * @Then /^there is no button to edit the project$/
  */
  public function thereIsNoButtonToEditTheProject()
  {
    $page = $this->getSession('goutte')->getPage();
    if ($page->findById("edit-project")) {
      throw new Exception("There is an edit button on page");
    }
  }

  /**
  * @Given /^I am allowed to access the project edit page$/
  */
  public function iAmAllowedToAccessTheProjectEditPage()
  {
    $session = $this->getSession('goutte');
    $editUrl = "/projects/edit/{$this->_project_id}";
    $session->visit($editUrl);
    
    if ($editUrl != parse_url($session->getCurrentUrl(), PHP_URL_PATH)) {
      throw new Exception("User is not allowed to edit project");
    }
  }

  /**
  * @Given /^I am not allowed to access the project edit page$/
  */
  public function iAmNotAllowedToAccessTheProjectEditPage()
  {
    $session = $this->getSession('goutte');
    $editUrl = "/projects/edit/{$this->_project_id}";
    $session->visit($editUrl);
    
    if ($editUrl == parse_url($session->getCurrentUrl(), PHP_URL_PATH)) {
      throw new Exception("User is allowed to edit project");
    }
  }

  /**
  * @Given /^I am on the detail page of a project by someone else$/
  */
  public function iAmOnTheDetailPageOfAProjectBySomeoneElse()
  {
    $this->iLogin();
    $session = $this->getSession('goutte');
    $session->visit('/behat/someoneElsesProject');
    $project = json_decode($raw = $session->getPage()->getText());
    var_dump($raw, $project);
    $session->visit("/projects/view/{$project->project_id}");
  }

  public function iLoginAsAdmin() {
    $page = $this->startSessionWithPage('/users/login')->getPage();
    var_dump($this->getSession('goutte')->getCurrentUrl());
    $page->fillField('username-field', 'admin');
    $page->fillField('password-field', 'admin');
    $page->pressButton('login-button');
  }

  /**
  * @Given /^I am admin$/
  */
  public function iAmAdmin()
  {
    $this->iLoginAsAdmin();
    $session = $this->getSession('goutte');
    $session->visit('/behat/checkAdmin');
    $info = json_decode($session->getPage()->getText());
    if (!$info->is_admin) {
      throw new Exception("User is not admin");
    }
  }

  /**
  * @Given /^I am not admin$/
  */
  public function iAmNotAdmin()
  {
    $this->iLogin();
    $session = $this->getSession('goutte');
    $session->visit('/behat/checkAdmin');
    $info = json_decode($session->getPage()->getText());
    if ($info->is_admin) {
      throw new Exception("User is admin");
    }
  }

  /**
  * @When /^I change any of \(([^\)]+)\)$/
  */
  public function iChangeFields($fields)
  {
    // Save key for subsequent step
    $this->key = uniqid();

    $page = $this->getSession('goutte')->getPage();

    // Save fields for subsequent step
    $this->fields = preg_split('/,\s*/', $fields);

    foreach ($this->fields as $field) {
      $fieldId = "{$field}-field";
      if (!($el = $page->findById($fieldId))) {
        throw new Exception("No field {$field}");
      }
      $value = $el->getAttribute('value');

      if (preg_match('/#@[^@]+@#/', $value)) {
        $value = preg_replace('/#@[^@]+@#/', "#@{$this->key}@#", $value);
      }
      else {
        $value .= "#@{$this->key}@#";
      }
      $page->fillField("{$field}-field", $value);
    }
    $page->pressButton('save-button');
  }

  /**
  * @Then /^my changes are saved$/
  */
  public function myChangesAreSaved()
  {
    $page = $this->getSession('goutte')->getPage();
    foreach ($this->fields as $field) {
      $fieldId = "{$field}-content";
      if (!($el = $page->findById($fieldId))) {
        throw new Exception("No content for {$field}");
      }
      $content = $field->getHtml();
      
      // $this->key from previous step
      if (strpos("#@{$this->key}@#", $content) === FALSE) {
        throw new Exception("Content not saved for field \"{$field}\"");
      }
    }
  }
} // class FeatureContext
