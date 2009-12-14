<?php

/**
 * sfDynamicsExperimentalClosureAPIJavascriptFilter
 *
 * Uses google's closure compiler via the HTTP POST API to compile the script.
 *
 * WARNING - WARNING - WARNING - WARNING - WARNING - WARNING - WARNING - WARNING
 *
 * This will make use of network to compile the script, and will probably have
 * very bad performances. More, you're giving an external service access to your
 * whole javascript source.
 *
 * Use it at your own risks.
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license MIT
 */

class sfDynamicsExperimentalClosureAPIJavascriptFilter extends sfDynamicsBaseAssetFilter
{
  protected function doFilter($code)
  {
    if (!function_exists('curl_init'))
    {
      throw new RuntimeException(sprintf('%s needs PHP curl extension.', get_class($this)));
    }

    $ch = curl_init('http://closure-compiler.appspot.com/compile');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=SIMPLE_OPTIMIZATIONS&js_code='.urlencode($code));
    $code = curl_exec($ch);
    curl_close($ch);

    return $code;
  }
}

