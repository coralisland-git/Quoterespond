<?php

namespace App\Http\Services;

use App\Http\Services\ShortLinkService;
use Carbon\Carbon;

class MagicLinkService
{
	static public function getTextWithClickLink($text, $client_id)
	{
		$link_position = strpos($text, 'bit.ly/');

		if ($link_position !== false) {
			$original_link = substr($text, $link_position, 14);
			$magic_link = ShortLinkService::make(config('app.url').'/magic/'.$client_id.'/'.$original_link, false);
    		$text = str_replace($original_link, $magic_link, $text);
		}

		return $text;
    }
}