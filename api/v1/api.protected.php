<?php
// This is not implemented just yet. Give it time... It might happen one day.
require_once 'api.php';

class LeumAPI extends API
{
	protected $user;

	public function __construct($request, $origin)
	{
		parent::__construct($request);

		$APIKey = 
		$User =

		if(!array_key_exists('apiKey', $this->request))
		{
			throw new Exception('No API Key provided');
		}
		else if (!$APIKey->verifyKey($this->request['apiKey'], $origin))
		{
			throw new Exception("Invalid API Key");
		}
		else if (array_key_exists('token', $this->request) && !$User->get('token', $this->request['token']))
		{
			throw new Exception("Invalid User Token");
		}
	}
}

?>
