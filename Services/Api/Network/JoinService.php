<?php
namespace Services\Api\Network;

use Quark\IQuarkIOProcessor;
use Quark\IQuarkPostService;
use Quark\IQuarkServiceWithCustomProcessor;

use Quark\QuarkDTO;
use Quark\QuarkJSONIOProcessor;
use Quark\QuarkServiceBehavior;
use Quark\QuarkSession;

/**
 * Class JoinService
 *
 * @package Services\Api\Network
 */
class JoinService implements IQuarkPostService, IQuarkServiceWithCustomProcessor {
	use QuarkServiceBehavior;

	/**
	 * @param QuarkDTO $request
	 *
	 * @return IQuarkIOProcessor
	 */
	public function Processor (QuarkDTO $request) {
		return new QuarkJSONIOProcessor();
	}

	/**
	 * @param QuarkDTO $request
	 * @param QuarkSession $session
	 *
	 * @return mixed
	 */
	public function Post (QuarkDTO $request, QuarkSession $session) {
		$signature = hash('sha256', $this->LocalSettings('HelixNodeSecret') . $request->nonce);

		if ($request->signature == $signature)
			return array(
				'status' => 400
			);
	}
}