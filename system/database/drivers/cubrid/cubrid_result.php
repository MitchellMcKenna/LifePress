<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 2.0.2
 * @filesource
 */

/**
 * CUBRID Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		Esen Sagynov
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_cubrid_result extends CI_DB_result {

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		return @cubrid_num_rows($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @return	int
	 */
	public function num_fields()
	{
		return @cubrid_num_fields($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Field Names
	 *
	 * Generates an array of column names
	 *
	 * @return	array
	 */
	public function list_fields()
	{
		return cubrid_column_names($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Field data
	 *
	 * Generates an array of objects containing field meta-data
	 *
	 * @return	array
	 */
	public function field_data()
	{
		$retval = array();

		$tablePrimaryKeys = array();

		while ($field = cubrid_fetch_field($this->result_id))
		{
			$F				= new stdClass();
			$F->name		= $field->name;
			$F->type		= $field->type;
			$F->default		= $field->def;
			$F->max_length	= $field->max_length;

			// At this moment primary_key property is not returned when
			// cubrid_fetch_field is called. The following code will
			// provide a patch for it. primary_key property will be added
			// in the next release.

			// TODO: later version of CUBRID will provide primary_key
			// property.
			// When PK is defined in CUBRID, an index is automatically
			// created in the db_index system table in the form of
			// pk_tblname_fieldname. So the following will count how many
			// columns are there which satisfy this format.
			// The query will search for exact single columns, thus
			// compound PK is not supported.
			$res = cubrid_query($this->conn_id,
				"SELECT COUNT(*) FROM db_index WHERE class_name = '" . $field->table .
				"' AND is_primary_key = 'YES' AND index_name = 'pk_" .
				$field->table . "_" . $field->name . "'"
			);

			if ($res)
			{
				$row = cubrid_fetch_array($res, CUBRID_NUM);
				$F->primary_key = ($row[0] > 0 ? 1 : null);
			}
			else
			{
				$F->primary_key = null;
			}

			if (is_resource($res))
			{
				cubrid_close_request($res);
				$this->result_id = FALSE;
			}

			$retval[] = $F;
		}

		return $retval;
	}

	// --------------------------------------------------------------------

	/**
	 * Free the result
	 *
	 * @return	void
	 */
	public function free_result()
	{
		if(is_resource($this->result_id) ||
			get_resource_type($this->result_id) == "Unknown" &&
			preg_match('/Resource id #/', strval($this->result_id)))
		{
			cubrid_close_request($this->result_id);
			$this->result_id = FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Data Seek
	 *
	 * Moves the internal pointer to the desired offset. We call
	 * this internally before fetching results to make sure the
	 * result set starts at zero
	 *
	 * @return	bool
	 */
	protected function _data_seek($n = 0)
	{
		return cubrid_data_seek($this->result_id, $n);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - associative array
	 *
	 * Returns the result set as an array
	 *
	 * @return	array
	 */
	protected function _fetch_assoc()
	{
		return cubrid_fetch_assoc($this->result_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @return	object
	 */
	protected function _fetch_object()
	{
		return cubrid_fetch_object($this->result_id);
	}

}

/* End of file cubrid_result.php */
/* Location: ./system/database/drivers/cubrid/cubrid_result.php */