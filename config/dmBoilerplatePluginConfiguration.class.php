<?php

// TODO: Add more configuration parameters

class dmBoilerplatePluginConfiguration extends sfPluginConfiguration
{
  protected $defaults = array(
    'htaccess.modify' => true,
    'htaccess.backup' => '-boilerplate-backup',
  );

  /**
   * Initializes the plugin.
   *
   * This method is called after the plugin's classes have been added to sfAutoload.
   *
   * @return boolean|null If false sfApplicationConfiguration will look for a config.php (maintains BC with symfony < 1.2)
   */
  public function initialize()
  {
    // Customize configuration with the `configureBoilerplate` ProjectConfiguration method if exists
    if (method_exists($this->configuration, 'configureBoilerplate'))
    {
      $this->configuration->configureBoilerplate($this);
    }

    // notify the dispatcher
    $this->dispatcher->notify(new sfEvent($this, 'dm_boilerplate.configuration', array(
      'configuration' => $this
    )));

    $this->generateHtaccess();

    // Initialization has been executed correctly
    return true;
  }

  public function generateHtaccess()
  {
    if ($this->getHtaccessModify())
    {
      $htPath = sfConfig::get('sf_web_dir') . '/.htaccess';
      $htBack = $htPath . $this->getHtaccessBackup();

      if (!file_exists($htBack))
      {
        // Generate the new file
        require_once(this->getRootDir() . '/config/helper/dmBoilerplatePluginHtaccess.class.php');
        dmBoilerplatePluginHtaccess::generate($htPath, $htBack);
      }
    }
  }

  // htaccess methods
  public function getHtaccessModify()
  {
    return $this->getConfig('htaccess.modify');
  }

  public function setHtaccessModify($check)
  {
    return $this->setConfig('htaccess.modify', (bool) $check);
  }

  public function getHtaccessBackup()
  {
    return $this->getConfig('htaccess.backup');
  }

  public function setHtaccessBackup($back)
  {
    return $this->setConfig('htaccess.backup', (string) $back);
  }

  public function getHtaccessFlag()
  {
    return $this->getConfig('htaccess.flag');
  }

  public function setHtaccessFlag($flag)
  {
    return $this->setConfig('htaccess.flag', (string) $flag);
  }

  /**
   *  Get a named application configuration value
   *
   *  @param  string  $name     The configuration to get the value
   *  @param  mixed   $default  The default value to return if not set
   *
   *  @return mixed   The value of the named application configuration
   */
  public function getConfig($name, $default = null)
  {
    return array_key_exists($name, $this->defaults) ? sfConfig::get('app_' . $this->name . '_' . $name, $this->defaults[$name]) : null;
  }

  /**
   *  Set a value to a named application configuration
   *
   *  @param  string  $name     The configuration to set the value
   *  @param  mixed   $valuet   The value to assign to the named application configuration
   *
   *  @return self    The current instance ($this)
   */
  public function setConfig($name, $value)
  {
    if (array_key_exists($name, $this->defaults))
    {
      sfConfig::set('app_' . $this->name . '_' . $name, $value);
    }
    return $this;
  }
}
