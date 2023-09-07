<?php
namespace Services\Api\Network;

use Models\Domain;
use Quark\IQuarkGetService;
use Quark\IQuarkIOProcessor;
use Quark\IQuarkServiceWithCustomProcessor;

use Quark\QuarkCollection;
use Quark\QuarkDTO;
use Quark\QuarkJSONIOProcessor;
use Quark\QuarkModel;
use Quark\QuarkSession;

use Models\Node;

/**
 * Class IndexService
 *
 * @package Services\Api\Network
 */
class IndexService implements IQuarkGetService, IQuarkServiceWithCustomProcessor {
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
	public function Get (QuarkDTO $request, QuarkSession $session) {
		/**
		 * @var QuarkCollection|Domain[] $domains
		 */
		$domains = QuarkModel::Find(new Domain());

		/**
		 * @var QuarkCollection|Node[] $nodes
		 */
		$nodes = QuarkModel::Find(new Node());

		return array(
			'status' => 200,
			'network' => array(
				'domains' => $domains->Extract(),
				'nodes' => $nodes->Extract()
			)
		);
	}
}