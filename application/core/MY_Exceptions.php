<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

	public function log_exception($severity, $message, $filepath, $line)
	{
		parent::log_exception($severity, $message, $filepath, $line);

		if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
			$level = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
			$output = 'Severity: '.$level.' --> '.$message.' '.$filepath.' '.$line.PHP_EOL;
			@file_put_contents('php://stderr', $output);
		}
	}
}
