<?php

class izarusFacebookPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('routing.load_configuration', array('izarusFacebookPluginRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}