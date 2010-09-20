<?php

class dmBoilerplatePluginHtaccess
{
  protected static $instance = null;
  protected $flag = '# START dmBoilerplatePlugin';

  public static function getInstance()
  {
    if (is_null(self::$instance))
    {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public static function generate($currentPath, $backupPath)
  {
    // Backup the current .htaccess contents
    $contents = file_get_contents($currentPath);
    file_put_contents($backupPath, $contents);
    
    // Compute new contents and write it to new .htaccess
    $contents = self::getInstance()->compute($contents);
    file_put_contents($currentPath, $contents);
  }
        
  protected function compute($contents)
  {
    // Get the content that will not be modified
    $pos = strripos($contents, $this->flag);
    $pos = (false === $pos) ? 0 : $pos + strlen($this->flag);

    $contents = substr($contents, 0, $pos);

    $contents = "## Modified by dmBoilerplatePlugin\n" . $contents ."\n\n". file_get_contents(dirname(__FILE__).'/dmBoilerplatePlugin.htaccess');
    return $contents;
  }

}
