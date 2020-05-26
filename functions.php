<?php
if ((float) PHP_VERSION < 8) {

/**
* Replacer for php 8's str_contain
* @param string $haystack The haystack
* @param string $needed The needle
* @return bool
*/
function str_contains(string $haystack , string $needle) : bool
{
	if (strpos($haystack, $needle) !== false) {
		return true;
	}

	return false;
}

/**
* Replacer for php 8's str_starts_with
* @param string $haystack The haystack
* @param string $needed The needle
* @return bool
*/
function str_starts_with(string $haystack , string $needle) : bool
{
	if (strpos($haystack, $needle) === 0) {
		return true;
	}

	return false;
}

}