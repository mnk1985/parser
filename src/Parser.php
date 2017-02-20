<?php 

namespace App;

use App\ParserInterface;

class Parser implements ParserInterface
{

	protected $url_sms;
	protected $url_skypeout;
	protected $client;
	protected $countryResult = [];

	public function __construct()
	{
		$this->client = new \GuzzleHttp\Client();
	}

	/**
	 * @param  string
	 * @param  string
	 * @return array
	 */
	public function getDataByCountry(string $toCountry, string $fromCountry = 'RU')
	{
		$this->generateUrls($toCountry, $fromCountry);
		$this->getSkypeoutData();
		$this->getSmsData();
		$result = $this->countryResult;
		unset($this->countryResult);
		return $result;
	}

	/**
	 * @return void
	 */
	protected function getSkypeoutData()
	{
		$destinations = $this->getDestinations($this->url_skypeout);

		foreach($destinations as $destination){
			$category = $this->getCategory($destination);

			$this->countryResult[$category][] = [
				'name' => $destination->name,
				'priceFormatted' => $destination->usageCharge->priceFormatted
			];
		}
	}

	/**
	 * @return void
	 */
	protected function getSmsData()
	{
		$destinations = $this->getDestinations($this->url_sms);

		if ($destinations)
		{
			foreach($destinations as $destination){
				$category = $this->getCategory($destination, 'sms');

				$this->countryResult[$category][] = [
					'name' => $destination->name,
					'priceFormatted' => $destination->usageCharge->priceFormatted
				];
			}
		}
	}

	/**
	 * @param  stdClass
	 * @param  string
	 * @return string
	 */
	protected function getCategory($destination = null, $type = 'skypeout')
	{
		if ($type == 'sms')
			return 'SMS';

		if($destination->type == 'landline' && strpos($destination->name, ' – ') !== false) 
			return 'Favourite';

		if($destination->type == 'landline' && strpos($destination->name, ' – ') === false) 
			return 'Country';

		if($destination->type == 'mobile')
			return 'Mobile';
	}

	/**
	 * @param  string
	 * @return array | Exception
	 */
	protected function getDestinations($url)
	{
		$response = $this->client->request('GET',  $url);

		$resultJson = $response->getBody()->getContents();

		$result = json_decode($resultJson);

		if ($result && isset($result->destinations))
			return $result->destinations;

		return null;
	}

	/**
	 * @param  string
	 * @param  string
	 * @return void
	 */
	protected function generateUrls(string $toCountry, string $fromCountry)
	{
		if(strlen($toCountry) != 2 || strlen($fromCountry) != 2){
			throw new \Exception('incorrent country code. must be 2 chars');
		}

		$this->url_sms = 'https://apps.skypeassets.com/rates/sms?_accept=2.0&billingCountry='.
			$fromCountry.
			'&currency=USD&destinationCountry='.
			$toCountry.
			'&expand=price%2Cpending&language=ru&originCountry='.
			$fromCountry.
			'&seq=18&service=sms';

		$this->url_skypeout = 'https://apps.skypeassets.com/rates/skypeout?_accept=2.0&billingCountry='.
			$fromCountry.
			'&currency=USD&destinationCountry='.
			$toCountry.
			'&expand=price%2Cpending&language=ru&originCountry='.
			$fromCountry.
			'&seq=18&service=skypeout';
	}

}