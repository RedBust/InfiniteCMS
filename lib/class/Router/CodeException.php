<?php
class Router_CodeException extends Exception
{
	protected $code;

	public function __construct($code)
	{
		$this->code = $code;
	}
	public function getHttpCode()
	{
		return $this->code;
	}
}