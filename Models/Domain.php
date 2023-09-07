<?php
namespace Models;

use Quark\IQuarkLinkedModel;
use Quark\IQuarkModel;
use Quark\IQuarkModelWithBeforeCreate;
use Quark\IQuarkModelWithBeforeSave;
use Quark\IQuarkModelWithDataProvider;
use Quark\IQuarkNullableModel;
use Quark\IQuarkStrongModel;

use Quark\Quark;
use Quark\QuarkDate;
use Quark\QuarkModel;
use Quark\QuarkModelBehavior;

/**
 * Class Domain
 *
 * @property string $id
 * @property string $hostname
 * @property QuarkDate $date_created
 * @property QuarkDate $date_updated
 *
 * @package Models
 */
class Domain implements IQuarkModel, IQuarkStrongModel, IQuarkModelWithDataProvider, IQuarkModelWithBeforeCreate, IQuarkModelWithBeforeSave, IQuarkLinkedModel, IQuarkNullableModel {
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
			'id' => Quark::GuID(),
			'hostname' => '',
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

	/**
	 * @param $raw
	 *
	 * @return mixed
	 */
	public function Link ($raw) {
		return QuarkModel::FindOne(new Domain(), array(
			'hostname' => $raw
		));
	}

	/**
	 * @return mixed
	 */
	public function Unlink () {
		return (string)$this->hostname;
	}
}