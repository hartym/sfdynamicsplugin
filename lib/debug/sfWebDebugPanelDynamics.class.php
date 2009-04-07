<?php

class sfWebDebugPanelDynamics extends sfWebDebugPanel
{
  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/config.png" alt="sfDynamicsPlugin informations" /> sfDynamics';
  }

  public function getPanelTitle()
  {
    return 'sfDynamicsPlugin informations';
  }

  public function getPanelContent()
  {
    $controller = sfContext::getInstance()->getController();

    $html = '
    <style type="text/css">
      div#sfWebDebugDynamics table ul,
      div#sfWebDebugDynamics table ul li
      {
        margin: 0;
        padding: 0;
        list-style-type: dash;
      }
      div#sfWebDebugDynamics table ul
      {
        padding-left: 16px;

      }
    </style>

    <table class="sfWebDebugLogs">
      <tr>
        <td>&nbsp;</td>
        <th>Javascript items</th>
        <th>Generated javascript</th>
        <th>Stylesheet items</th>
        <th>Generated stylesheet</th>
      </tr>'."\n";
    $line_nb = 0;

    $manager = sfDynamics::getManager();

    foreach ($manager->getPackages() as $packageName => $package)
    {

      $html .= '<tr>';
      $html .= '<th>'.$packageName.'</th>';

      foreach (array('Javascripts'=>'js', 'Stylesheets'=>'css') as $assetType => $extension)
      {
        if ($package->{'has'.$assetType}())
        {
          $html .= '<td>';
          $html .= '<ul>';
          foreach ($package->{'get'.$assetType}() as $javascript)
          {
            $html .= '<li>'.$javascript.'</li>';
          }
          $html .= '</ul>';
          $html .= '</td>';

          $html .= '<td>';
          $url = $controller->genUrl(sfDynamicsRouting::uri_for($packageName, $extension));
          $html .= sprintf('<a href="%s" target="_blank">%s</a>', $url, basename($url));
          $html .= '</td>';
        }
        else
        {
          $html .= '<td colspan="2" align="center">not available</td>';
        }
      }

      $html .= '</tr>';
    }
    $html .= '</table>';

    return '<div id="sfWebDebugDynamics">'.$html.'</div>';
  }

  static public function listenToLoadPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('dynamics', new sfWebDebugPanelDynamics($event->getSubject()));
  }

}
