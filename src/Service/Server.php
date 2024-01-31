<?php

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';
require_once __DIR__ . '/../Component/';

use Approach\Service\Service;
use Approach\Service\target;
use Approach\Service\format;
use Approach\Service\flow;
use ClimbUI\Component;

class Server extends Service
{
	public static $registrar = [];

	public static function View($action)
	{
		$title = $action['Climb']['title'];

		return [[
			'REFRESH' => ['#result' => '<div>Form Submitted! Title: ' . $title . '</div>'],
		]];
	}

	public static function Click($action)
	{
		$name = $action['support']['name'];
		$tabsForm = Component\getTabsForm();
		
		return [[
			'REFRESH' => ['#some_content' => $tabsForm->render()],
		]];
	}

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
	) {

		self::$registrar['Climb']['Save'] = function ($context) {
			return self::View($context);
		};
		self::$registrar['Climb']['Click'] = function ($context) {
			return self::Click($context);
		};
		parent::__construct($flow, $auto_dispatch, $format_in, $format_out, $target_in, $target_out, $input, $output, $metadata);
	}

	public function Process(?array $payload = null): void
	{
		$payload = $payload ?? $this->payload;

		$action = $payload[0]['support'];

		foreach ($payload[0] as $verb => $intent) {
			foreach ($intent as $scope => $instruction) {
				foreach ($instruction as $command => $context) {
					if ($command == 'Climb') {
						// print_r($command);
						$this->payload = self::$registrar[$command][$context]($action);
					}
				}
			}
		}
	}
}
