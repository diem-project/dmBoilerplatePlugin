The plugin use the recommandation from [Html5Boilerplate](http://html5boilerplate.com/ "Html5Boilerplate") by modifying the `.htaccess` file, adding the assets (CSS & JS), and changing the layout.
Compatible with [Diem 5.1] (not tested on other versions).

Change the front layout
-----------------------

> Set the front layout helper class service to `dmBoilerplatePluginFrontLayoutHelper`

<br />
This can be done in the *front/config/dm/services.yml*

    # front/config/dm/services.yml
    parameters:

      layout_helper.class:    dmBoilerplatePluginFrontLayoutHelper

<br />
Or in the front layout template *front/modules/dmFront/templates/layout.php*

    // @var dmBoilerplatePluginFrontLayoutHelper
    $helper = $sf_context->get('layout_helper',
      'dmBoilerplatePluginFrontLayoutHelper');
    
    // ... default template code


Confiure the plugin
-------------------

> The plugin configuration can be set in the `ProjectConfiguration` class via a `configureBoilerplate` user method.
> It can also be configured in the *front/config/app.yml* configuration file, or via a connection to the `dm_boilerplate.configuration` event.
> The description on how to achieve that is described below with the plugin default configuration.

<br />
The plugin configuration instance is passed in as its argument, or can be retrieved through the `getPluginConfiguration` method

    public function configureBoilerplate(
      dmBoilerplatePluginConfiguration $config)
	  {
	    // The plugin instance can also be retrieved as follow
	    //$config = $this->getPluginConfiguration('dmBoilerplatePlugin');
	    
	    // Configure dmBoilerplatePlugin, default values
	    $config->setHtaccessModify(true);
	    $config->setHtaccessBackup('-boilerplate-backup');
	  }
	  
<br />
Configure via the *front/config/app.yml* file

    # front/config/app.yml, default values
    dmBoilerplatePlugin:
      htaccess.modify: true
	    htaccess.backup: "-boilerplate-backup"

<br />
Configuration via `dm_boilerplate.configuration` event, for example in the ProjectConfiguration.
Event parameters: `array('configuration' => dmBoilerplatePluginConfiguration instance)`

    // config/ProjectConfiguration.class.php, default values
    public function setup()
    {
      // Connect to `dm_boilerplate.configuration`
      // to configure the plugin later
      $this->dispatcher->connect('dm_boilerplate.configuration',
        array($this, 'listenToBoilerplateConfiguration'));
      
      // ... previous code
      
      $this->enablePlugins(array('dmBoilerplatePlugin'));
    }
    
    /**
     *  Configure `dmBoilerplatePlugin` via a event listenner
     *  @param  sfEvent $event  The event `configuration` holds the plugin
     */
    public function listenToBoilerplateConfiguration(sfEvent $event)
    {
      $config = $event['configuration'];
      
      $config->setHtaccessModify(true); // default
      $config->setHtaccessBackup('-boilerplate-backup'); // default
    }

