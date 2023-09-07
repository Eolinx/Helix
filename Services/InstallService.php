<?php
namespace Services;

use Models\Domain;
use Models\Node;
use Quark\IQuarkTask;

use Quark\Quark;
use Quark\QuarkCLIViewBehavior;
use Quark\QuarkCollection;
use Quark\QuarkDTO;
use Quark\QuarkFile;
use Quark\QuarkHTTPClient;
use Quark\QuarkINIIOProcessor;
use Quark\QuarkJSONIOProcessor;
use Quark\QuarkModel;
use Quark\QuarkURI;

/**
 * Class InstallService
 *
 * @package Services
 */
class InstallService implements IQuarkTask {
	use QuarkCLIViewBehavior;

	/**
	 * @param int $argc
	 * @param array $argv
	 *
	 * @return mixed
	 */
	public function Task ($argc, $argv) {
		$this->ShellView(
			'Helix Installation',
			'Welcome to Helix installation' . "\r\n\r\n" .
			' Generating NodeID and NodeSecret...'
		);

		$nodeID = Quark::GuID();
		$nodeSecret = Quark::GuID();

		echo '',
			' * NodeID: ' . $this->ShellLineSuccess($nodeID, true),
			' * NodeSecret: ' . $this->ShellLineSuccess($nodeSecret, true);

		echo "\r\n", 'Enter primary Domain: ';
		/**
		 * @var QuarkModel|Domain $domain
		 */
		$domain = new QuarkModel(new Domain());
		$domain->hostname = \readline();

		echo "\r\n", 'Enter current Node\'s hostname: ';
		/**
		 * @var QuarkModel|Node $nodeCurrent
		 */
		$nodeCurrent = new QuarkModel(new Node());
		$nodeCurrent->hostname = \readline();
		$nodeCurrent->domain = $domain;

		echo 'Applying settings... ';
		$source = new QuarkFile(__DIR__ . '/../.devops/docker/filesystem/app/runtime/application.ini', true);

		$ini = new QuarkINIIOProcessor();
		$settings = $source->Decode($ini);

		$settings->LocalSettings->HelixNodeID = $nodeID;
		$settings->LocalSettings->HelixNodeSecret = $nodeSecret;

		$target = new QuarkFile(__DIR__ . '/../runtime/application.ini');

		if (!$target->Encode($ini, $settings)->SaveContent()) {
			echo 'FAILURE';
			return;
		}
		echo 'OK', "\r\n";

		$dbURI = QuarkURI::FromURI($settings->DataProviders->HELIX_DB);
		Quark::Config()->DataProvider(HELIX_DB)->URI($dbURI);

		echo 'Add primary domain... ';
		if (QuarkModel::Exists(new Domain(), array('hostname' => $domain->hostname))) {
			echo 'OK', "\r\n";
			echo $this->ShellLineWarning('Domain already exists', true);
		}
		else {
			if (!$domain->Create()) {
				echo 'FAILURE';
				return;
			}
			echo 'OK', "\r\n";
		}

		echo 'Add current node... ';
		if (QuarkModel::Exists(new Node(), array('hostname' => $nodeCurrent->hostname))) {
			echo 'OK', "\r\n";
			echo $this->ShellLineWarning('Node already exists', true);
		}
		else {
			$nodeCurrent->id = $nodeID;
			if (!$nodeCurrent->Create()) {
				echo 'FAILURE';
				return;
			}
			echo 'OK', "\r\n";
		}

		echo 'Retrieving same domain nodes... ';
		$records = dns_get_record($domain->hostname, DNS_TXT);
		$hosts = array();
		foreach ($records as $i => &$record) {
			if (!isset($record['entries'][0])) continue;
			if (!preg_match('#^helix-nodes=([a-z0-9\-_,]+)#i', $record['entries'][0] . ',', $record_nodes)) continue;

			$hosts = explode(',', trim($record_nodes[1], ','));
			break;
		}

		/**
		 * @var QuarkCollection|Domain[] $domains
		 */
		$domains = new QuarkCollection(new Domain());
		$hostname = '';

		foreach ($hosts as $i => &$host) {
			$hostname = $host . '.' . $domain->hostname;
			//if ($hostname == $nodeCurrent->hostname) continue;
			if ($hostname != $nodeCurrent->hostname) continue;

			$response = QuarkHTTPClient::To(
				'http://' . $hostname . '/--helix/api/network',
				QuarkDTO::ForGET(),
				new QuarkDTO(new QuarkJSONIOProcessor())
			);

			Quark::Trace($response);

			if (!isset($response->network)) {
				echo "\r\n", $this->ShellLineError('Response from "' . $hostname . '" doesn\'t have valid structure: missing "network" object', true);
				continue;
			}
			if (!isset($response->network->domains) || !is_array($response->network->domains)) {
				echo "\r\n", $this->ShellLineError('Response from "' . $hostname . '" doesn\'t have valid structure: missing "network.domains" list', true);
				continue;
			}
			if (!isset($response->network->nodes) || !is_array($response->network->nodes)) {
				echo "\r\n", $this->ShellLineError('Response from "' . $hostname . '" doesn\'t have valid structure: missing "network.nodes" list', true);
				continue;
			}

			$domains->PopulateModelsWith($response->network->domains);
			//Quark::Trace($domains);

			if (!$domains->Exists(array('hostname' => $domain->hostname))) {
				echo "\r\n", $this->ShellLineError('Response from "' . $hostname . '" doesn\'t have valid structure: list "network.domains" doesn\'t contain current\' node domain, maybe diverged network', true);
				continue;
			}

			foreach ($response->network->nodes as $item) {
				/**
				 * @var QuarkModel|Node $node
				 */
				$node = new QuarkModel(new Node(), $item);

				if ($node->hostname == $nodeCurrent->hostname) {
					echo "\r\n", $this->ShellLineError('Can not add node "' . ($node->hostname) . '" of domain "' . $domain->hostname . '": same node as current', true);
					continue;
				}

				if (!$node->Create()) {
					echo "\r\n", $this->ShellLineError('Can not add node "' . ($node->hostname) . '" of domain "' . $domain->hostname . '". Check application.log for details.', true);
					continue;
				}

				// TODO: call join request
			}
		}

		if (!true) {
			echo 'FAILURE';
			return;
		}
		echo 'OK', "\r\n";

		echo "\r\n", $this->ShellLineSuccess('Installation successful!' . "\r\n" . 'Please keep NodeID and NodeSecret - they will be useful for attaching main application to Helix', true);
	}
}