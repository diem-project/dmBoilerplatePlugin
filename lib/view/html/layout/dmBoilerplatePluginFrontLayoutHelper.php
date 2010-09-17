<?php

/**
 *  Customize the `layout_helper` service to use http://html5boilerplate.com recommendation
 *  Original `dmFrontLayoutHelper` file path: %DM_FRONT_DIR%/lib/view/html/layout/dmFrontLayoutHelper.php
 */
class dmBoilerplatePluginFrontLayoutHelper extends dmFrontLayoutHelper
{

  protected $assetsBaseName = 'dmBoilerplatePlugin';

  /**
   * Base name in the `config/dm/assets.yml` configuration file
   * 
   * @return  string  The base name in the assets.yml configuration file
   */
  public function getAssetsBaseName()
  {
    return $this->assetsBaseName;
  }

  /**
   * Replace the closing tag to use the correct strategy
   *
   * @param   string  $html  The HTML string to fix
   * @return  string  The fixed HTML string
   */
  protected function fixCloseTag($html)
  {
    if ($this->isHtml5)
    {
      // Replace the closing tag by the correct one
      $html = str_replace(' />', '>', $html);
    }
    return $html;
  }

  /**
   * HTML tag rendering
   * Customized to use `class="no-js"` attribute for Modernizr
   *
   * @see dmCoreLayoutHelper
   */
  public function renderHtmlTag()
  {
    $htmlTag = parent::renderHtmlTag();

    $htmlTag = substr_replace($htmlTag, ' class="no-js">', strlen($htmlTag) - 1);

    return $htmlTag;
  }

  /**
   * META http-equiv tag rendering
   * Customized to use the correct closing tag strategy and the correct `charset` / `Content-Type`
   * Add a conditionnal comment hack for IE
   *
   * @see dmCoreLayoutHelper
   */
  public function renderHttpMetas()
  {
    // Get the rendered HttpMetas
    $metas = parent::renderHttpMetas();

    // Leading HTML is `charset` if HTML5 or the rendered HttpMetas otherwise
    $html = $this->isHtml5 ? '<meta charset="' . sfConfig::get('sf_charset', 'utf-8') . "\">\n" : $metas;

    // Add the conditionnal comment hack for MSIE
    $html .= '<!--[if IE]><![endif]-->' . "\n";

    // Add the rendered HttpMetas if not added yet (if is HTML5)
    $html .= $this->isHtml5 ? $metas : '';

    // Fix the close tag
    $html = $this->fixCloseTag($html);

    return $html;
  }

  /**
   * Add a fix for mobile viewport
   * @see http://j.mp/mobileviewport ; http://davidbcalhoun.com/2010/viewport-metatag
   *
   * device-width : Occupy full width of the screen in its current orientation
   * initial-scale = 1.0 retains dimensions instead of zooming out if page height > device height
   * maximum-scale = 1.0 retains dimensions instead of zooming in if page width < device width
   *
   * @see dmFrontLayoutHelper
   */
  protected function getMetas()
  {
    $metas = parent::getMetas();
    $metas['viewport'] = 'width=device-width; initial-scale=1.0; maximum-scale=1.0;';

    return $metas;
  }

  /**
   * META tag rendering
   * Customized to use the correct closing tag strategy
   *
   * @see dmCoreLayoutHelper
   */
  public function renderMetas()
  {
    return $this->fixCloseTag(parent::renderMetas());
  }

  /**
   * Stylesheets rendering
   * Add corresponding boilerplate stylesheets
   * Use the correct closing tag strategy
   *
   * @see dmCoreLayoutHelper
   */
  public function renderStylesheets()
  {
    // Add stylesheets
    $this->getService('response')
      ->addStylesheet($this->assetsBaseName . '.base', 'first')
      ->addStylesheet($this->assetsBaseName . '.media', 'last')
      ->addStylesheet($this->assetsBaseName . '.handheld', 'last', array('media' => 'handheld'));

    // Render the `response` stylesheets
    $html = parent::renderStylesheets();

    // Fix the close tag and return the result
    return $this->fixCloseTag($html);
  }

  /**
   * IE Html5 fix rendering
   * Customized to NOT use html5shiv as Modernizr integrate it
   */
  public function renderIeHtml5Fix()
  {
    return '';
  }

  /**
   * Render Javascripts included in the HEAD tag
   * Add Modernizr in the HEAD tag
   *
   * @see dmCoreLayoutHelper
   */
  public function renderHeadJavascripts()
  {
    // Add javascripts, Modernizr in HEAD tag
    $dmJsHead = sfConfig::get('dm_js_head_inclusion');
    $dmJsHead[$this->assetsBaseName . '.modernizr'] = true;
    sfConfig::set('dm_js_head_inclusion', $dmJsHead);
    $this->getService('response')->addJavascript($this->assetsBaseName . '.modernizr', 'first');

    return parent::renderHeadJavascripts();
  }

  /**
   * BODY tag rendering
   * Customized to add `class="ie%VERSION%"` tag attribute
   *
   * @see dmFrontLayoutHelper
   */
  public function renderBodyTag($options = array())
  {
    $html = '';

    $options = dmString::toArray($options);
    $class = dmArray::get($options, 'class', array());

    $options['class'] = array_merge($class, array('ie6'));
    $html .= '<!--[if lt IE 7 ]> '
      . parent::renderBodyTag($options) . ' <![endif]-->' . "\n";

    $options['class'] = array_merge($class, array('ie7'));
    $html .= '<!--[if IE 7 ]> '
      . parent::renderBodyTag($options) . ' <![endif]-->' . "\n";

    $options['class'] = array_merge($class, array('ie8'));
    $html .= '<!--[if IE 8 ]> '
      . parent::renderBodyTag($options) . ' <![endif]-->' . "\n";

    $options['class'] = array_merge($class, array('ie9'));
    $html .= '<!--[if IE 9 ]> '
      . parent::renderBodyTag($options) . ' <![endif]-->' . "\n";

    $options['class'] = $class;
    $html .= '<!--[if (gt IE 9)|!(IE)]><!--> '
      . parent::renderBodyTag($options) . ' <!--<![endif]-->' . "\n";

    return $html;
  }

  protected function getGoogleAnalyticsCode($gaKey)
  {
    return "<script type=\"text/javascript\">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '" . $gaKey . "']);
_gaq.push(['_trackPageview']);
(function(d, t) {
var ga = d.createElement(t), s = d.getElementsByTagName(t)[0];
ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == d.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
s.parentNode.insertBefore(ga, s);
})(document, 'script');
</script>";
  }

}