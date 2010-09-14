<?php

class dmBoilerplatePluginHtaccess
{

  static public function generate($currentPath, $backupPath)
  {
    // Backup the current .htaccess contents
    $contents = file_get_contents($currentPath);
    file_put_contents($backupPath, $contents);
    
    // Compute new contents and write it to new .htaccess
    $contents = self::compute($contents);
    file_put_contents($currentPath, $contents);
  }
        
  static protected function compute($contents)
  {
    // Get the content that will not be modified
    $flag = '# dmBoilerplatePlugin below';
    $pos = strripos($contents, $flag);
    if(false === $pos)
    {
      throw new ErrorException('The following line is missing in SF_WEB_DIR/.htaccess file: '.$flag);
    }
    $contents = substr($contents, 0, $pos + strlen($flag));

    $contents = "## Modified by dmBoilerplatePlugin\n" . $contents ."\n\n". file_get_contents(dirname(__FILE__).'/dmBoilerplatePlugin.htaccess');
    return $contents;
  }

}
