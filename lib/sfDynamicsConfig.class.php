<?php

/**
 * sfDynamicsConfig
 *
 * Centralise simple configuration triggers.
 *
 * @todo allow overiding this in app.yml (partially implemented)
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 */
class sfDynamicsConfig
{
  /**
   * Cache for concatenated asset filter chain. Multiple call won't
   * reinstanciate the filters. Keys are asset types.
   */
  static protected $concatenatedAssetFilterChainCache = array();
  static protected $concatenatedAssetFilterChainDefaults = array(
      'javascript' => array('sfDynamicsJSMinJavascriptFilter'),
      'stylesheet' => array('sfDynamicsSimpleStylesheetFilter'),
    );

  /**
   * Internal symfony cache usage setting
   *
   * It is recommended to leave this to true, even when sf_debug is true. The
   * cache will be ignored if its timestamp is older than one of the asset files.
   *
   * @return boolean
   */
  static public function isCacheEnabled()
  {
    return sfConfig::get('app_sfDynamicsPlugin_enable_cache', true);
  }

  /**
   * Are we checking modification time of each package file to know if cache is
   * valid, or not? To keep good balance between performances, and
   * developpability, we'll check only in debug mode.
   *
   * @return boolean
   */
  static public function isCacheUpToDateCheckEnabled()
  {
    return sfConfig::get('sf_debug');
  }

  /**
   * Web server static file cache usage setting
   *
   * It is recommended to activate this in production environment, for a huge
   * performance boost.
   *
   * @return boolean
   */
  static public function isSupercacheEnabled()
  {
    return sfConfig::get('app_sfDynamicsPlugin_enable_supercache', !sfConfig::get('sf_debug'));
  }

  static public function isGroupingEnabledFor($type)
  {
    switch ($type)
    {
      case 'javascript':
      case 'stylesheet':
        return !sfConfig::get('sf_debug');

      default:
        throw new BadMethodCallException('Invalid asset type');
    }
  }

  static public function isStylesheetImportResolutionEnabled($package)
  {
    return true;
  }

  /**
   * from __future__ import relative_paths_resolution
   */
  static public function isStylesheetRelativePathsResolutionEnabled($package)
  {
    return sfConfig::get('app_sfDynamicsPlugin_enable_experimental_relative_paths_resolution', false);
  }

  /**
   * getSuperCacheDir - path under sf_web_dir in which we'll store the supercached assets
   *
   * @return string
   */
  static public function getSuperCacheDir()
  {
    return sfConfig::get('app_sfDynamicsPlugin_supercache_web_path', 'dynamics');
  }

  static public function getAssetsPositionInHead()
  {
    $position = sfConfig::get('app_sfDynamicsPlugin_assets_position_in_head', 'append');

    if (!in_array($position, array('append', 'prepend')))
    {
      throw new sfDynamicsConfigurationException('Invalid assets position in head.');
    }

    return $position;
  }

  static public function getConcatenatedAssetFilterChainFor($type)
  {
    if (!isset(self::$concatenatedAssetFilterChainCache[$type]))
    {
      self::$concatenatedAssetFilterChainCache[$type] = new sfDynamicsAssetFilterChain();

      $config = sfConfig::get(sprintf('app_sfDynamicsPlugin_concatenated_%s_filter_chain', $type), self::$concatenatedAssetFilterChainDefaults[$type]);

      foreach($config as $filterClassName)
      {
        self::$concatenatedAssetFilterChainCache[$type]->addByName($filterClassName);
      }
    }

    return self::$concatenatedAssetFilterChainCache[$type];
  }
}
