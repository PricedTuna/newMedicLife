<?php
/**
 * Utility functions for the application
 */

/**
 * Ensures data is properly encoded in UTF-8 for json_encode()
 * This function recursively processes arrays and converts strings to UTF-8
 *
 * @param mixed $data The data to be encoded
 * @return mixed The UTF-8 encoded data
 */
function utf8ize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = utf8ize($value);
        }
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}
