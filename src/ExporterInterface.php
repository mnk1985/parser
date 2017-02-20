<?php 

namespace App;

interface ExporterInterface
{
	/**
	 * @param  string
	 * @param  array
	 * @return void
	 */
	public function exportTo(string $fileName, array $destinations);
}