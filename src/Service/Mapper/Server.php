<?php
// Load Composer's autoloader
// require_once __DIR__ . '../../../support/lib/vendor/autoload.php';

namespace Service\Mapper;

use Approach\path;
use Approach\Scope;
use Approach\Service\Service;
use Approach\Service\flow;
use Approach\Service\format;
use Approach\Service\target;

class Mapper extends Service
{
	/**
	 * @var flow $flow  The flow of the service (the final output is going into the system or out to the user)
	 * @var bool $auto_dispatch  Whether or not to automatically dispatch the service
	 * @var format|null $format_in  The format of the input
	 * @var format|null $format_out  The format of the output
	 * @var target|null $target_in  The target of the input - set to target::stream to capture input stream from the connection (or other standard input)
	 * @var target|null $target_out  The target of the output - set to target::stream to output to the connection (or other standard output)
	 * @var mixed|null $input  The input - Leave null to stream in by default
	 * @var mixed|null $output  The output - Leave null to stream out by default
	 */
	public flow $flow = flow::in;
	public bool $auto_dispatch = true;
	public format $format_in = format::json;
	public format $format_out = format::json;
	public target $target_in = target::transfer;
	public target $target_out = target::transfer;
	public $input = null;
	public $output = null;
	public mixed $metadata = [];
	public ?bool $register_connection = true;


	// PreProcess() is called before JSON is decoded. 
	// We want to run a function based on the command in the JSON, so we will use Process() instead

	public function Process(array $payload = null): void
	{

		$this->payload = $payload ?? $this->payload;

		// basically just 
		// case COMMAND_NAME: $this->CommandName($request['support'])
		//
		// done.  just like a CLI has  user> command arg arg arg
		//
		// or a function has command(arg,arg,arg)
		//
		// command is command, support is array of args

		foreach ($this->payload as $i => $request) {
			switch ($request['command']) {
				
				/*
				case 'MyCommand':
					$this->MyCommand( $request['support'] );
					break;
				*/

				/**
				 * TODO Make ListResources() instead of ListDatasets
				 * for now we will just use static data in ListDatasets to build the UI
				 */
				case 'ListDatasets':
					$this->ListDatasets();		// Scan for source datasets. We skip sending $command because it is not needed
					break;


				case 'FetchCurrentBinding':
					$this->OLD_FetchCurrentBinding($request['support']);
					break;

				case 'FetchDatasetHeader':
					$this->OLD_FetchDatasetHeader($request['support']);
					break;


				default:
					throw new \Exception('Unsupported command in ' . static::class . ': ' . $request['command']);
					break;
			}
		}
	}

	public function ListDatasets()
	{


		// Generally use Scope::GetPath with the path enum to find project directories the developer is using
		$base_path = Scope::GetPath(path::services) . 'TokenMapper/';


		// In the original TokenMapper, it scanned specific directories
		// In this one, we will look at all subdirectories to build the sources list
		// sourcetype::source    such as    component::Banner   or   data::users
		$available = [];



		// Get all subdirectories of the base path
		$directories = glob($base_path . '*', GLOB_ONLYDIR);

		// Loop through each directory
		foreach ($directories as $directory) {
			// Get the directory name
			$directory_name = basename($directory);

			// Add the name of all JSON files in the directory to the available list
			$files = glob($directory . '/*.json');
			foreach ($files as $file) {
				$source = $directory_name . '::' . basename($file, '.json');
				$available[] = $source;
			}
		}

		// Return the available sources
		$this->payload[] = $available;
		return $this->payload;
	}



	public function OLD_ListDatasets()
	{
		$DataPath = $base_path = Scope::GetPath(path::resource);
		$dir = $file = __DIR__ . '\\..\\..\\support\\service\\';

		$available_datasets = [];
		foreach (glob($DataPath . '/*.php') as $filename) {
			$filename_parts = explode('/', $filename);
			$file = end($filename_parts);
			$available_datasets[] = 'dataset::' . substr($file, 0, -4);
		}
		foreach (glob($dir . '/mls_samples/*.json') as $filename) {
			$filename_parts = explode('/', $filename);
			$file = end($filename_parts);
			$available_datasets[] = 'mls_sample::' . substr($file, 0, -5);
		}
		foreach (glob($dir . '/data_dictionary/*.json') as $filename) {
			$filename_parts = explode('/', $filename);
			$file = end($filename_parts);
			$available_datasets[] = 'data_dictionary::' . substr($file, 0, -5);
		}
		foreach (glob($dir . '/tagging/*.json') as $filename) {
			$filename_parts = explode('/', $filename);
			$file = end($filename_parts);
			$available_datasets[] = 'tagging::' . substr($file, 0, -5);
		}
		foreach (glob($dir . '/component_bindings/*.json') as $filename) {
			$filename_parts = explode('/', $filename);
			$file = end($filename_parts);
			$available_datasets[] = 'component::' . substr($file, 0, -5);
		}
		$this->payload[] = $available_datasets;
		return $this->payload;
	}





	public function OLD_FetchDatasetHeader($support)
	{
		$tmp = explode('::', $support['dataset']);


		$type = $tmp[0];
		$source = $tmp[1];
		unset($tmp);

		$composed = false;

		if (isset($support['composed'])) {
			$composed = $support['composed'];
		}

		$source_meta = [];


		// if ($type === 'dataset')
		// {

		// 	$file = file_get_contents(__DIR__ . '\\..\\..\\support\\service\\mls_samples\\' . $source . '.json');
		// 	$json = json_decode($file, true);



		// 	foreach ($json as $fieldname => $sample_value) {
		// 		$source_meta[] = [
		// 			'name'      =>  $fieldname,
		// 			'type'      =>  'text',
		// 			'composed'  =>  $composed == false ? [] : [$fieldname],
		// 			'transformer' =>  null
		// 		];
		// 	}

		// 	//dd($source_meta);
		// }

		if ($type === 'mls_sample') {
			$file = file_get_contents(__DIR__ . '\\..\\..\\support\\service\\mls_samples\\' . $source . '.json');
			$json = json_decode($file, true);


			foreach ($json as $fieldname => $sample_value) {
				$source_meta[] = [
					'name'      =>  $fieldname,
					'type'      =>  'text',
					'composed'  =>  $composed == false ? [] : [$fieldname],
					'transformer' =>  null
				];
			}
		}

		if ($type === 'component') {
			$file = file_get_contents(__DIR__ . '\\..\\..\\support\\service\\component_bindings\\' . $source . '.json');
			$json = json_decode($file, true);

			foreach ($json as $fieldname => $sample_value) {
				$source_meta[] = [
					'name'      =>  $fieldname,
					'type'      =>  'text',
					'composed'  =>  empty($sample_value['composed']) ? [] : $sample_value['composed'],
					'transformer' =>  null
				];
			}
		}

		if ($type === 'data_dictionary') {
			$file = file_get_contents(__DIR__ . '\\..\\..\\support\\service\\data_dictionary\\' . $source . '.json');
			$json = json_decode($file, true);

			foreach ($json as $fieldname => $sample_value) {
				$source_meta[] = [
					'name'      =>  $fieldname,
					'type'      =>  'text',
					'composed'  =>  $composed == false ? [] : [$fieldname],
					'transformer' =>  null
				];
			}
		}

		if ($type === 'tagging') {
			$file = file_get_contents(__DIR__ . '\\..\\..\\support\\service\\tagging\\' . $source . '.json');
			$json = json_decode($file, true);

			foreach ($json as $fieldname => $sample_value) {
				$source_meta[] = [
					'name'      =>  $fieldname,
					'type'      =>  'text',
					'composed'  =>  $composed == false ? [] : [$fieldname],
					'transformer' =>  null
				];
			}
		}
		$this->payload = [$source_meta];
		return $this->payload;
	}

	public function OLD_FetchCurrentBinding($support)
	{
		global $db;
		$fetch = $support['dataset'];

		// These came from old framework's LoadObject() on the datamaps table, this JSON was made by SettingMapper's save button
		// WHERE `dataset` = "component::TextEmbed" AND `tag`="settings"
		$text_embed_settings = json_decode(
			'[{"name":"self_id","type":"Suite_TextInput","composed":[{"source":"component::TextEmbed","field":"_self_id","meta":{"confirmed":true}}],"formatter":null,"sanitizer":null,"default":null,"label":null,"disabled":false,"hidden":true,"groupNumber":null,"required":false,"toolbar":false,"toolbarIcons":[],"source":"","extra":""},{"name":"owner","type":"Suite_TextInput","composed":[{"source":"component::TextEmbed","field":"usr","meta":{"confirmed":true}}],"formatter":null,"sanitizer":null,"default":null,"label":null,"disabled":false,"hidden":true,"groupNumber":null,"required":false,"toolbar":false,"toolbarIcons":[],"source":"","extra":""},{"name":"text content","type":"Suite_RichText","composed":[{"source":"component::TextEmbed","field":"content","meta":{"confirmed":true}}],"formatter":null,"sanitizer":null,"default":null,"label":"","disabled":false,"hidden":false,"groupNumber":null,"required":false,"toolbar":false,"toolbarIcons":[],"source":"","extra":""}]'
		);

		// These came from old framework's LoadObject() on the datamaps table, this JSON was made by TokenMapper's save button
		// WHERE `dataset` = "component::TextEmbed" AND `tag`="tokens"
		$text_embed_tokens = json_decode(
			'[{"name":"_self_id","type":"","composed":[{"source":"dataset::text_embeds","field":"id","meta":{"confirmed":true}}],"formatter":null,"transformer":null,"default":null},{"name":"usr","type":"","composed":[{"source":"dataset::text_embeds","field":"agent","meta":{"confirmed":true}}],"formatter":null,"transformer":null,"default":null},{"name":"content","type":"","composed":[{"source":"dataset::text_embeds","field":"embed","meta":{"confirmed":true}}],"formatter":null,"transformer":null,"default":null}]'
		);


		$binding = $text_embed_settings;


		if (empty($binding))
			return $this->payload[] = $this->FetchDatasetHeader(['dataset' => $fetch, 'composed' => false]);
		else
			$this->payload[] = $binding;

		return $this->payload;
	}

	public function PreProcess(?array $payload = null): void
	{
		$this->metadata['command'] = [];
		foreach ($this->payload as $incoming) {
			$this->metadata['command'][] = $incoming;
		}
	}
	public function PostProcess(?array $payload = null): void
	{
		$i = 0;
		foreach ($this->payload as $encoded_payload) {
			$origin = $this->metadata['command'][$i] ?? '"{}"';
			$encoded_payload = '{"payload":' . $encoded_payload . ',"origin":' . $origin . '}';
		}
	}

	public function FetchDatasetHeader($a)
	{
		return true;
	}
}
