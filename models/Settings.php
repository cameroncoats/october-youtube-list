<?php namespace Cameroncoats\YouTube\Models;

use Model;
use Validation;

/**
 * YouTube Videos settings model
 *
 * @author Brendon Park
 *
 */
class Settings extends Model
{

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'cameroncoats_ytvideos_settings';

    public $settingsFields = 'fields.yaml';

}
