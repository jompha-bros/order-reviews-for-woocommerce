<?php
function orfw_sanitize_array( $arrayA )
{
    if ( !is_array($arrayA) )
        return array();
    
    $arrayB = array();

    foreach ( $arrayA as $key => $value )
    {
        $key = (is_numeric($key)) ? intval($key) : sanitize_key($key);

        switch ( gettype($value) )
        {
            case 'boolean':
                $value = boolval($value);
                break;
            
            case 'integer':
                $value = intval($value);
                break;
            
            case 'double':
                $value = doubleval($value);
                break;
            
            case 'string':
                $value = sanitize_text_field($value);
                break;
            
            case 'array':
                $value = orfw_sanitize_array($value);
                break;
            
            default:
                $value = '';
                break;
        }

        $arrayB[ $key ] = $value;
    }

    return $arrayB;
}
