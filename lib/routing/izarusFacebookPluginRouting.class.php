<?php

class izarusFacebookPluginRouting
{
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $event->getSubject()->prependRoute('facebook_signin', new sfRoute('/facebook/login', array('module' => 'sfFacebookAuth', 'action' => 'signin')));
    $event->getSubject()->prependRoute('facebook_connect', new sfRoute('/facebook/connect', array('module' => 'sfFacebookAuth', 'action' => 'connect')));
  }
}

