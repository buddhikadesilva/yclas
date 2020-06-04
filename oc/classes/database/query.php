<?php
/**
 * Database query wrapper.  See [Parameterized Statements](database/query/parameterized) for usage and examples.
 *
 * @package    Kohana/Database
 * @category   Query
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class Database_Query extends Kohana_Database_Query {

	/**
	 * Execute the current query on the given database.
	 *
	 * @param   mixed    $db  Database instance or name of instance
	 * @param   string   result object classname, TRUE for stdClass or FALSE for array
	 * @param   array    result object constructor arguments
	 * @param   Model 	 $model to know which model are we caching ;)
	 * @return  object   Database_Result for SELECT queries
	 * @return  mixed    the insert id for INSERT queries
	 * @return  integer  number of affected rows for all other queries
	 */
	public function execute($db = NULL, $as_object = NULL, $object_params = NULL, $model = NULL)
	{
		if ( ! is_object($db))
		{
			// Get the database instance
			$db = Database::instance($db);
		}

		if ($as_object === NULL)
		{
			$as_object = $this->_as_object;
		}

		if ($object_params === NULL)
		{
			$object_params = $this->_object_params;
		}

		// Compile the SQL query
		$sql = $this->compile($db);

		if ($this->_lifetime !== NULL AND $this->_type === Database::SELECT)
		{
			// Set the cache key based on the database instance name and SQL
			$cache_key = 'Database::query("'.$db.'", "'.$sql.'")';

			//listing the cache entries for this model
			if ($model!==NULL AND is_subclass_of($model,'ORM'))
				$model::cache_list($cache_key);
			
			// Read the cache first to delete a possible hit with lifetime <= 0
			if (($result = Kohana::cache($cache_key, NULL, $this->_lifetime)) !== NULL
				AND ! $this->_force_execute)
			{
				// Return a cached result
				return new Database_Result_Cached($result, $sql, $as_object, $object_params);
			}
		}

		// Execute the query
		$result = $db->query($this->_type, $sql, $as_object, $object_params);

		if (isset($cache_key) AND $this->_lifetime > 0)
		{
			// Cache the result array
			Kohana::cache($cache_key, $result->as_array(), $this->_lifetime);
		}

		return $result;
	}

} // End Database_Query
