<?php

/**
 *  Customize the `layout_helper` service to use http://html5boilerplate.com recommendation
 *  Original `dmFrontLayoutHelper` file path: %DM_FRONT_DIR%/lib/view/html/layout/dmFrontLayoutHelper.php
 */
class dmBoilerplatePluginFrontLayoutHelper extends dmFrontLayoutHelper
{

  protected $assetsBaseName = 'dmBoilerplatePlugin', $ieComment = "<!--[if %s]>%s<![endif]-->\n";

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
   * BODY tag rendering
   * Customized to add `class="ie%VERSION%"` tag attribute
   *
   * @see dmFrontLayoutHelper
   */
  public function renderBodyTag($options = array())
  {
    $html = '';
    $spaced = ' %s ';
    $opt = 'class';

    // Save the initail `class` attribute
    $options = dmString::toArray($options);
    $class = dmArray::get($options, $opt, array());

    $options[$opt] = array_merge($class, array('ie6'));
    $html .= sprintf($this->ieComment, 'lt IE 7', sprintf($spaced, parent::renderBodyTag($options)));

    $options[$opt] = array_merge($class, array('ie7'));
    $html .= sprintf($this->ieComment, 'IE 7', sprintf($spaced, parent::renderBodyTag($options)));

    $options[$opt] = array_merge($class, array('ie8'));
    $html .= sprintf($this->ieComment, 'IE 8', sprintf($spaced, parent::renderBodyTag($options)));

    $options[$opt] = array_merge($class, array('ie9'));
    $html .= sprintf($this->ieComment, 'IE 9', sprintf($spaced, parent::renderBodyTag($options)));

    $options[$opt] = $class;
    $html .= sprintf($this->ieComment, '(gt IE 9)|!(IE)', sprintf('<!-->' . $spaced . '<!--', parent::renderBodyTag($options)));

    return $html;
  }

  /**
   * HEAD section rendering
   * Customized to fix tag ends
   *
   * @see dmCoreLayoutHelper
   * @return string The HEAD html section
   */
  public function renderHead()
  {
    return $this->fixTagEnd(parent::renderHead());
  }

  /**
   * Replace the tag end to use the correct syntax
   *
   * @param   string  $html  The HTML string to fix
   * @return  string  The fixed HTML string
   */
  protected function fixTagEnd($html)
  {
    if ($this->isHtml5())
    {
      // Replace the tag end by the correct one
      //$html = str_replace(' />', '>', $html);
      $html = preg_replace('#\s*/>#', '>', $html);
    }
    return $html;
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
    $html = $this->isHtml5() ? sprintf("<meta charset=\"%s\">\n", sfConfig::get('sf_charset', 'utf-8')) : $metas;

    // Add the conditionnal comment hack for MSIE
    $html .= sprintf($this->ieComment, 'IE', '');

    // Add the rendered HttpMetas if not added yet (if is HTML5)
    $html .= $this->isHtml5() ? $metas : '';

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
   * IE Html5 fix rendering
   * Customized to NOT use html5shiv as Modernizr integrate it
   */
  public function renderIeHtml5Fix()
  {
    return '';
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
      ->addStylesheet($this->getAssetsBaseName() . '.base', 'first')
      ->addStylesheet($this->getAssetsBaseName() . '.media', 'last')
      ->addStylesheet($this->getAssetsBaseName() . '.handheld', 'last', array('media' => 'handheld'));

    // Render the `response` stylesheets
    return parent::renderStylesheets();
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
    $dmJsHead[$this->getAssetsBaseName() . '.modernizr'] = true;
    sfConfig::set('dm_js_head_inclusion', $dmJsHead);
    $this->getService('response')
      ->addJavascript($this->getAssetsBaseName() . '.modernizr', 'first', array('head_inclusion' => true));

    return parent::renderHeadJavascripts();
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

  /**
   * Base name in the `config/dm/assets.yml` configuration file
   *
   * @return  string  The base name in the assets.yml configuration file
   */
  public function getAssetsBaseName()
  {
    return $this->assetsBaseName;
  }

}