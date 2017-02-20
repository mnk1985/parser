<?php 

namespace App;

interface ParserInterface
{
	/**
	 * @param  string
	 * @param  string
	 * @return array
	 */
	public function getDataByCountry(string $toCountry, string $fromCountry = 'RU');
}