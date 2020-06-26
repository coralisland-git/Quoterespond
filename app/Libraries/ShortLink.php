<?php

namespace App\Libraries;

use DivArt\ShortLink\Facades\ShortLink as Shorten;

class ShortLink
{
	static public function make($url, $withProtocol)
    {
    	return Shorten::bitly($url, $withProtocol);
    }

    static public function expand($shortUrl)
    {
    	return Shorten::expand($shortUrl);
    }
}