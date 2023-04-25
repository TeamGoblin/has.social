<?php

class JET_Object {

    public function __construct()
    {

    }

    public static function initObject(object $class, bool $use_definition = false)
    {

        try
        {
            if( method_exists($class, 'define') )
            {
                $object_id_queue = $class->define();

                $objects = [];

                $class_name = get_class($class);

                if( property_exists( $class, 'id') )
                {
                    $class_id = 'id';
                }
                elseif( property_exists( $class, strtolower($class_name) . '_id') )
                {
                    $class_id = strtolower($class_name) . '_id';
                }
                else
                {
                    throw new Exception('Can not find class identification.');
                }

                foreach($object_id_queue as $object_id)
                {
                    $this_id = array_keys(get_object_vars($object_id));
                    $this_id = $this_id[0];
                    $objects[] = new $class_name([$class_id => $object_id->{$this_id}]);
                }

                return collect($objects);
            }
            else
            {
                throw new Exception('define method does not exist.');
            }
        }
        catch(Exception $e)
        {
            error_log('Caught exception: ' . $e->getMessage() . ' in ' . __CLASS__ . ' ' . __LINE__ . "\n");
        }

    }

}
