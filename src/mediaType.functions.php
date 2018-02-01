<?php

class MediaTypes
{
	static $knownTypes = array();

	static function RegisterType($mediaType, $mimeType, $priority)
	{
		if(isset($knownTypes["$mimeType"]))
		{
			if($priority > $knownTypes["$mimeType"]["priority"]);
			$knownTypes["$mimeType"] = ["type"=>$mediaType, "priority"=>$priority];
		}
		else
			$knownTypes["$mimeType"] = [$mediaType, $priority];
	}
	static function GetTypeFor($mediaItem)
	{
		$mimeType = $mediaItem->GetMimeType();

		if(isset($knownTypes[$mimeType]))
		return $knownTypes[$]
	}
	public function GetTypeOf($mimeType)
	{
		if(isset($knownTypes[$mimeType]))
			return $knownTypes[$mimeType];
		else
			throw new Exception("Unknown mime-type. Please create or modify a type to support this file.", 1);
			
	}
}

?>