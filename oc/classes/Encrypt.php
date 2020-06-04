<?php defined('SYSPATH') or die('No direct script access.');

class Encrypt extends Kohana_Encrypt {


	/**
	 * Returns a singleton instance of Encrypt. An encryption key must be
	 * provided in your "encrypt" configuration file.
	 *
	 *     $encrypt = Encrypt::instance();
	 *
	 * @param   string  $name   configuration group name
	 * @return  Encrypt
	 */
	public static function instance($name = NULL, array $config = NULL)
	{
		if ($name === NULL)
		{
			// Use the default instance name
			$name = Encrypt::$default;
		}

		if ( ! isset(Encrypt::$instances[$name]))
		{
			if ($config === NULL)
			{
				// Load the configuration data
				$config = Kohana::$config->load('encrypt')->$name;
			}

			//here we override we use to encrypt using the one of the installation, needs to be 32 chars...we duplicate it since hash_key is 16
			$config['key'] = Core::config('auth.hash_key').Core::config('auth.hash_key');

			if ( ! isset($config['key']))
			{
				// No default encryption key is provided!
				throw new Kohana_Exception('No encryption key is defined in the encryption configuration group: :group',
					array(':group' => $name));
			}

			// Create a new instance
			Encrypt::$instances[$name] = new Encrypt($config);
		}

		return Encrypt::$instances[$name];
	}

}
