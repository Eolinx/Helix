<?php
namespace Models;

use Quark\IQuarkModel;
use Quark\IQuarkModelWithBeforeCreate;
use Quark\IQuarkModelWithBeforeSave;
use Quark\IQuarkModelWithDataProvider;
use Quark\IQuarkStrongModel;
use Quark\QuarkModelBehavior;

/**
 * Class Entity
 *
 * @package Models
 */
class Entity implements IQuarkModel, IQuarkStrongModel, IQuarkModelWithDataProvider, IQuarkModelWithBeforeCreate, IQuarkModelWithBeforeSave {
	use QuarkModelBehavior;
}