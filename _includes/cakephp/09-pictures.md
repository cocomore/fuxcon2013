Our CakePHP implementation treats pictures and derived formats a bit like stepchildren. To keep the implementation small, pictures are not represented by model classes but are rather uploaded into the file system directly. Also, we only support JPEG as upload format.

The bad thing about this simplistic approach is that in order to check if a project has an uploaded picture, we have to check the file system, a potentially slow operation. Also, we save all pictures in a single directory, a thing you wouldn't want to do if you have more than a few thousand pictures because then file system access will become even slower.

Controllers don't do anything special to handle pictures.

The views use a special [helper](https://github.com/emersonsoares/ThumbnailsHelper-for-CakePHP) in file app/View/Helper/ThumbnailHelper.php to create derived images in the fly. These are then saved in the file system. Through a proper rewrite configuration, the web server delivers these cached derivatives directly, without going through the process of re-creating the derivatives again.

The helper uses special directories to save the derivatives:

![CakePHP thumbnails]({{ site.url }}/fuxcon2013/img/cakephp-thumbnails.png)

The same helper is used in the edit forms to show an image thumbnail of an already existing picture:

{% highlight php %}
<div class="pull-right thumbnail">
	<?php
  echo $this->Thumbnail->render('project/' . 1 . '.jpg', array(
    'width' => 160, 
    'height' => 160, 
    'resizeOption' => 'auto',
  )); 
  ?>
</div>
<?php
echo $this->Form->input('picture', array(
  'type' => 'file',
  'id' => 'picture-field'
));
?>
{% endhighlight %}

The handling of file uploads is done in the Project model class in the before- and afterSave callbacks:

{% highlight php %}
<?php
/**
 * Before saving the project, check an uploaded picture
 */
public function beforeSave($options = array()) {
  if (!isset($this->data[$this->alias]['picture'])) {
    return TRUE;
  }
  $file = $this->data[$this->alias]['picture'];
  if ($file['error'] === UPLOAD_ERR_NO_FILE) {
    return TRUE;
  }
  if ($file['error'] !== UPLOAD_ERR_OK) {
    return FALSE;
  }
  if (strpos($file['type'], 'image/jpeg') !== 0) {
    return FALSE;
  }
  return TRUE;
}

/**
 * After saving the project, save an uploaded picture
 */
public function afterSave($created) {
  if (!isset($this->data[$this->alias]['picture'])) {
    return;
  }
  $file = $this->data[$this->alias]['picture'];

  if ($file['error'] === UPLOAD_ERR_OK) {
    if (!move_uploaded_file($file['tmp_name'], IMAGES . 'project' . DS . $this->id . '.jpg')) {
      throw new Exception("Failed to move file: " 
        . posix_strerror(posix_get_last_error()));
    }
  }
}
{% endhighlight %}

A picture upload is checked before saving a project and actually stored after saving when we have the project ID.
