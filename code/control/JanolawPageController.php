<?php

namespace SixF\Janolaw\Control;

use PageController;
use SilverStripe\SiteConfig\SiteConfig;
use SixF\Janolaw\Extension\JanolawSiteConfig;

class JanolawPageController extends PageController {

  /**
   * @return void
   */
  public function init() {
    parent::init();
    // Injector::inst()->get(LoggerInterface::class)->debug("JanolawPageController => init()");

    //
    $config = SiteConfig::current_site_config();

    if (JanolawSiteConfig::has_valid_config($config) && JanolawSiteConfig::must_update($config)) {
      // Injector::inst()->get(LoggerInterface::class)->debug("  |- Must update!!!");
      $config->Sync();
    }




  }
}
