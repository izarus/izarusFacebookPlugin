<?php

class sfFacebook
{
  protected $facebook = null;
  protected $user = null;
  protected $profile = null;

  public function __construct($in_canvas = false){
    $config = array(
      'appId'   => sfConfig::get('app_facebook_app_id'),
      'secret'  => sfConfig::get('app_facebook_app_secret'),
      'allowSignedRequest' => $in_canvas,
      );

    $this->facebook = new Facebook($config);
    $this->user = $this->facebook->getUser();
    if ($this->user){
      try {
        $this->profile = $this->facebook->api('/me');
      } catch (FacebookApiException $e) {
        $this->user = NULL;
      }
    }
  }

  public function getUser() {
    return $this->facebook->getUser();
  }

  public function getLoginUrl(){
    return $this->facebook->getLoginUrl(array(
      'scope' => 'email',
      ));
  }

  public function getLogoutUrl(){
    return $this->facebook->getLogoutUrl();
  }

  public function getUserProfile() {
    return $this->profile;
  }

  public function findUserByEmail() {
    $table = sfConfig::get('app_facebook_model_class','sfGuardUser').'Table';
    $email_column = sfConfig::get('app_facebook_model_email_column','email_address');

    if ($this->profile) {
      return $table::getInstance()->findOneBy($email_column,$this->profile['email']);
    } else {
      return null;
    }
  }
}