<?php

class MyValidation
{
	/**
	 * Validate if there is no controll char
	 *
	 * @param   string
	 * @return  true|Exception
	 */
	public static function _validation_no_controll($val)
	{
		if (preg_match('/\A[[:^cntrl:]]*\z/u', $val) === 1)
		{
			return true;
		}
		else
		{
			\Log::error(
				'Invalid controll charactors: '.
				\Input::uri().' '.
				urlencode($val).' '.
				\Input::ip().
				' "'.\Input::user_agent().'"'
			);
			throw new HttpInvalidInputException('Invalid input data');
		}
	}
	
	/**
	 * Validate for select, radio, checkbox
	 *
	 * @param   string|array
	 * @param   array  valid options
	 * @return  true|Exception
	 */
	public static function _validation_in_array($val, $compare)
	{
		if (Validation::_empty($val))
		{
			return true;
		}
		
		if ( ! is_array($val))
		{
			$val = array($val);
		}
		
		foreach ($val as $value)
		{
			if ( ! in_array($value, $compare))
			{
				throw new HttpInvalidInputException('Invalid input data');
			}
		}

		return true;
	}
	
	/**
	 * Validate for not required array input
	 *
	 * @param   null|array
	 * @param   array  valid options
	 * @return  true|Exception
	 */
	public static function _validation_not_required_array($val)
	{
		if (is_array($val))
		{
			return true;
		}
		else
		{
			return array();
		}
	}
}
