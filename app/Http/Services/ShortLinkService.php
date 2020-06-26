<?php

namespace App\Http\Services;

use App\Libraries\ShortLink;

class ShortLinkService
{
	const PREFIX = 'bit.ly/';
	const LINK_LENGTH = 14;

	static public function make($url, $protocol = true)
	{
		return ShortLink::make($url, $protocol);
	}

	static public function cutShortLink($text)
	{
		$link_pos = strpos($text, self::PREFIX);
		if ($link_pos !== false) {
			return substr($text, $link_pos, self::LINK_LENGTH);
		}
		return '';
	}

	static public function expand($link)
	{
		return ShortLink::expand($link);
	}
}