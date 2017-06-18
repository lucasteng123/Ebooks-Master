<?php
class MiscTools {
	function get_calling_class() {
		// Copypaste from StackOverflow

	    //get the trace
	    $trace = debug_backtrace();

	    // Get the class that is asking for who awoke it
	    $class = $trace[1]['class'];

	    // +1 to i cos we have to account for calling this function
	    for ( $i=1; $i<count( $trace ); $i++ ) {
	        if ( isset( $trace[$i] ) ) // is it set?
	             if ( $class != $trace[$i]['class'] ) // is it a different class
	                 return $trace[$i]['class'];
	    }
	}
}