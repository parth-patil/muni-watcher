<?php

Class MuniWatcher
{
	protected $route;
	protected $direction;
	protected $stop;
	protected $runContinous;

	public $sleepSecs = 30;

	public function __construct($route,$direction,$stop,$runContinous=true)
	{
		$this->route = $route;
		$this->direction = $direction;
		$this->stop = $stop;
		$this->runContinous = true;
	}

	public function run()
	{
		if ($this->runContinous)
		{
			while(1)
			{
				$predictions = $this->getPredictions();
				$this->displayPredictions($predictions);
				sleep($this->sleepSecs);
			}
		}
		else
		{
			$predictions = $this->getPredictions();
			$this->displayPredictions($predictions);
		}
	}

	protected function getPredictions()
	{
		$query = "http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a=sf-muni&" .
							"r={$this->route}&d={$this->direction}&s={$this->stop}";

		$xmlString = file_get_contents($query);
		$xml = simplexml_load_string($xmlString); 
		$predictions = array();
		foreach ($xml->predictions->direction->prediction as $prediction)
		{
			$predictions[] = trim($prediction['minutes']);
		}

		return $predictions;
	}

	protected function displayPredictions($predictions)
	{
		$msg = "In mins : " . join(", ", $predictions);
		`notify-send "Prediction for route : {$this->route}" "$msg" -t 3000`;
		echo $msg . "\n";
	}
}

//$query = http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a=sf-muni&r=10&d=10_IB1&s=6189&ts=6188
$watcher = new MuniWatcher(10,'10_IB1',6189);
$watcher->sleepSecs = 20;
$watcher->run();

