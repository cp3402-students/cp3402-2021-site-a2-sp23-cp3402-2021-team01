<?php

namespace IfSo\PublicFace\Services\TriggersService\Handlers;

require_once( plugin_dir_path ( __DIR__ ) . 'chain-handler-base.class.php');
require_once(IFSO_PLUGIN_BASE_DIR . 'services/license-service/license-service.class.php');
require_once(IFSO_PLUGIN_BASE_DIR . 'services/impressions-service/impressions-service.class.php');

use IfSo\Services\LicenseService;
use IfSo\Services\ImpressionsService;

class ImpressionsHandler extends ChainHandlerBase {
	public function handle($context) {
		// TODO create this service under IfSoServices namespace (Already exists)
		$license = LicenseService\LicenseService::get_instance()->get_license();

		// TODO create this service under IfSoServices namespace
		$impression_service = ImpressionsService\ImpressionsService::get_instance();

		$impression_service->increment();
		$impression_service->handle($license);

		return $this->handle_next($context);
	}
}