<?php

namespace ClimbUI\Render;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;

/**
 * Class UserProfile
 * 
 * This class represents a user profile and extends the Render class.
 */
class UserProfile extends HTML
{
	public $image;
	public $heading;
	public $username;

	public function __construct($img_src, $heading, $username)
	{
		$this->tag = 'div';
		$this->attributes = ['id' => 'focus'];
		$this->nodes[] = $image_container = new HTML(tag: 'div');
		$this->image = new HTML(
			tag: 'img',
			attributes: [
				'width' => '64', 'src' => $img_src,
				'alt' => 'profile image'
			]
		);
		$this->nodes[] = $text_container = new HTML(tag: 'div');
		$this->heading = new HTML(tag: 'h3', content: $heading);
		$this->username = new HTML(tag: 'p', attributes: ['class' => 'gray'], content: $username);
	}
}