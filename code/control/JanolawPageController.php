<?php

namespace SixF\Janolaw\Control;

use PageController;
use SilverStripe\SiteConfig\SiteConfig;
use SixF\Janolaw\Extension\JanolawSiteConfig;

class JanolawPageController extends PageController
{

  /**
   * @return void
   */
  public function init()
  {
    parent::init();
    // Injector::inst()->get(LoggerInterface::class)->debug("JanolawPageController => init()");

    //
    $config = SiteConfig::current_site_config();

    //
    if (JanolawSiteConfig::must_sync($config)) {

      $config->Sync();
    }
  }
}
