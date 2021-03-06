<?php

namespace Exposable;

class Model extends \Orm\Model
{
	/**
	 * @var  array  contains a list of model fields which can be exposed on an API
	 */
	protected static $_exposable_properties = array();

	/**
	 * Exposable computed/dynamic model fields
	 * 
	 * @param  Model $model The current model to computed exposable fields on
	 * @return array computed/dynamic fields to expose
	 */
	protected static function _exposable_computed($model) {}

	/**
	 * Expose
	 * 
	 * Get exposable data for model(s) provided. Will expose fields specified in the
	 * $_exposable array and any computed fields returned in an array from the _exposable()
	 * function
	 * @param  array/model $models Model(s) to expose
	 * @return array
	 */
	public static function expose($models)
	{
		$exposed = array();
		if (is_array($models))
		{
			foreach ($models as $model)
			{
				$exposed[] = self::_expose_one($model);
			}
			return $exposed;
		}
		else {
			return self::_expose_one($models);
		}
	}

	protected static function _expose_one($model)
	{
		$class = get_called_class();
		$exposed = array();
		foreach ($class::$_exposable_properties as $key => $property)
		{
			if (is_array($property)) {
				foreach ($property as $sub) {
					if ($sub != '__flatten') {
						if ($property[0] == '__flatten')
							$exposed[$sub] = $model->$key->$sub;
						else
							$exposed[$key][$sub] = $model->$key->$sub;
					}
				}
			}
			else {
				$exposed[$property] = $model->$property;
			}
		}
		$dynamic = $class::_exposable_computed($model);
		if (is_array($dynamic))
			return array_merge($exposed, $dynamic);
		return $exposed;
	}
}