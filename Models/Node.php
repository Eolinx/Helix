<?php
namespace Models;

use Quark\IQuarkModel;
use Quark\IQuarkModelWithBeforeCreate;
use Quark\IQuarkModelWithBeforeSave;
use Quark\IQuarkModelWithDataProvider;
use Quark\IQuarkStrongModel;

use Quark\Quark;
use Quark\QuarkDate;
use Quark\QuarkModelBehavior;

/**
 * Class Node
 *
 * @property string $id
 * @property string $hostname
 * @property string $domain
 * @property QuarkDate $date_created
 * @property QuarkDate $date_updated
 *
 * @package Models
 */
class Node implements IQuarkModel, IQuarkStrongModel, IQuarkModelWithDataProvider, IQuarkModelWithBeforeCreate, IQuarkModelWithBeforeSave {
	use QuarkModelBehavior;

	/**
	 * @return string
	 */
	public function DataProvider () {
		return HELIX_DB;
	}

	/**
	 * @return mixed
	 */
	public function Fields () {
		return array(
			'id' => '',
			'hostname' => '',
			'domain' => $this->LazyLink(new Domain()),
			'date_created' => QuarkDate::NowUTC(),
			'date_updated' => QuarkDate::NowUTC()
		);
	}

	/**
	 * @return mixed
	 */
	public function Rules () {
		// TODO: Implement Rules() method.
	}

	/**
	 * @param $options
	 *
	 * @return mixed
	 */
	public function BeforeCreate ($options) {
		// TODO: Implement BeforeCreate() method.
	}

	/**
	 * @param $options
	 *
	 * @return mixed
	 */
	public function BeforeSave ($options) {
		$this->date_updated = QuarkDate::NowUTC();
	}
}