<?php

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Service\Service;
use Approach\Service\target;
use Approach\Service\format;
use Approach\Service\flow;

class Server extends Service
{
	public function __construct(
		flow $flow = flow::in,
		bool $auto_dispatch = false,
		?format $format_in = format::json,
		?format $format_out = format::json,
		?target $target_in = target::stream,
		?target $target_out = target::stream,
		$input = [Service::STDIN],
		$output = [Service::STDOUT],
		mixed $metadata = [],
		bool $register_connection = true
	)
    {
        parent::__construct($flow, $auto_dispatch, $format_in, $format_out, $target_in, $target_out, $input, $output, $metadata);
	}

	// public target $target_in  = target::stream;
	// public target $target_out = target::stream;


	public function Process(?array $payload = null): void
	{
		$payload = $payload ?? $this->payload;
		$this->payload = $payload;

		// foreach($this->payload as $key => $value)
		// {
		// 	switch($key){
		// 		case 'REFRESH': {
		// 			$this->payload['REFRESH'] = true;
		// 			break;
		// 		}
		// 		case 'NEW': {
		// 			$this->payload['NEW'] = true;
		// 			break;
		// 		}
		// 	}
		// }

	}

	
}

