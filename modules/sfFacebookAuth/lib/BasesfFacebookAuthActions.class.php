<?php

class BasesfFacebookAuthActions extends sfActions
{
  public function executeSignin(sfWebRequest $request)
  {
    if ($request->getParameter('redirect')) {
      $this->getUser()->setFlash('redirect',$request->getParameter('redirect'));
    }

    $facebook = new sfFacebook();
    if (!$facebook->getUser()) {
      return $this->redirect($facebook->getLoginUrl());
    } else {

      $profile = $facebook->getUserProfile();

      $user = sfGuardUserTable::getInstance()->findOneBy(sfConfig::get('app_facebook_guard_uid_column','facebook_uid'),$profile['id']);
      if (!$user) $user = sfGuardUserTable::getInstance()->findOneBy('email_address',$profile['email']);

      if (!$user) {
        $user = new sfGuardUser();
        $user->setEmailAddress($profile['email']);
        $user->setFirstName($profile['first_name']);
        $user->setLastName($profile['last_name']);
        $user->set(sfConfig::get('app_facebook_guard_uid_column','facebook_uid'),$profile['id']);
        $user->save();
      }

      $this->getUser()->signIn($user);

      if ($this->getUser()->hasFlash('redirect')) {
        return $this->redirect($this->getUser()->getFlash('redirect'));
      } else {
        return $this->redirect(sfConfig::get('app_facebook_after_signin_url','@homepage'));
      }
    }
  }

  public function executeConnect()
  {
    $this->forward404Unless($this->getUser()->isAuthenticated());

    $facebook = new sfFacebook();
    if (!$facebook->getUser()) {
      return $this->redirect($facebook->getLoginUrl());
    } else {

      $profile = $facebook->getUserProfile();

      // Check si otro usuario usa la cuenta facebook
      $other_user = sfGuardUserTable::getInstance()->findOneBy(sfConfig::get('app_facebook_guard_uid_column','facebook_uid'),$profile['id']);
      if ($other_user && $other_user->getId() != $this->getUser()->getGuardUser()->getId()){
        throw new Exception("Error. Otro usuario ya tiene asociado esta cuenta Facebook.");
      }

      // Check si otro usuario usa el mismo email
      $other_user = sfGuardUserTable::getInstance()->findOneBy('email_address',$profile['email']);
      if ($other_user && $other_user->getId() != $this->getUser()->getGuardUser()->getId()){
        throw new Exception("Error. Otro usuario ya tiene asociado el email de la cuenta Facebook.");
      }

      // Conecta cuenta Facebook con usuario logeado
      $user = $this->getUser()->getGuardUser();
      $user->set(sfConfig::get('app_facebook_guard_uid_column','facebook_uid'),$profile['id']);
      $user->save();

      return $this->redirect(sfConfig::get('app_facebook_after_signin_url','@homepage'));
    }
  }
}