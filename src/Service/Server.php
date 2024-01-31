<?php

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Service\Service;
use Approach\Service\target;
use Approach\Service\format;
use Approach\Service\flow;
use Approach\Render\HTML;

class Server extends Service
{
	public static $registrar = [];

	public static function View($action)
	{
		return [[
			'REFRESH' => ["#some_content" => "<div>Hello</div>"],
		]];
	}

	public static function Click($action)
	{
		$name = $action['name'];

		$html = new HTML(tag: 'div', classes: ['some_class'], content: 'Hello ' . $name . ' Bro! How you doing?');

		return [[
			'REFRESH' => ["#some_content" => $html->render()],
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

		self::$registrar['Climb']['New'] = function ($context) {
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
					$this->payload = self::$registrar[$command][$context]($action);
				}
			}
		}
	}
}
