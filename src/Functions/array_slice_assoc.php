<?php 

/**
 * if array_slice_assoc not exists, we add it.
 */
if (!function_exists('array_slice_assoc')) {
    
    function array_splice_assoc(array &$original, int $offset, int $length = 0, $replacement = null) {
        $slice = array_slice($original, 0, $offset, true);
    
        if (!is_null($replacement)) {
            // cast to array
            $replacementArray = is_array($replacement) ? $replacement : [$replacement];
    
            $slice = array_merge($slice, $replacementArray);
        }
    
        $original = array_merge($slice, array_slice($original, $offset+$length, null, true));
    
        return $original;
    }
    
}
