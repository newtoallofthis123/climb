<?php

namespace ClimbUI\Render;
require_once __DIR__ . '/../../support/lib/vendor/autoload.php';
use Approach\Render;

/**
 * Class UserProfile
 * 
 * This class represents a user profile and extends the Render class.
 */
class UserProfile extends Render\HTML
{
	public $image;
	public $heading;
	public $username; 

	public function __construct($img_src, $heading, $username)
	{
		/**
		 * TODO: Fix this $#!T
		 *
         * Stack Trace::
         * 
         * [Mon Jan 22 20:39:24 2024] 127.0.0.1:32928 [500]: GET / - Uncaught Error: Typed property Approach\Render\HTML::$classes must not be accessed before initialization in /home/noobscience/approach/climb/support/lib/approach/approach/Render/HTML/Properties.php:47
            Stack trace:
            #0 /home/noobscience/approach/climb/support/lib/approach/approach/Render/XML.php(44): Approach\Render\HTML->RenderHead()
            #1 /home/noobscience/approach/climb/support/lib/approach/approach/Render/XML.php(45): Approach\Render\XML->RenderCorpus()
            #2 /home/noobscience/approach/climb/support/lib/approach/approach/Render/Container.php(109): Approach\Render\XML->RenderCorpus()
            #3 /home/noobscience/approach/climb/index.php(11): Approach\Render\Container->render()
			#4 {main}
            thrown in /home/noobscience/approach/climb/support/lib/approach/approach/Render/HTML/Properties.php on line 47
         */
		$this->tag = 'div';
		$this->attributes = ['id'=>'focus'];
		$this->nodes[] = $image_container = new Render\HTML(tag:'div');
			$this->image = new Render\HTML(
				tag:'img', 
				attributes:['width'=>'64', 'src'=>$img_src, 
				'alt'=>'profile image']
			);
		$this->nodes[] = $text_container = new Render\HTML(tag:'div');
			$this->heading = new Render\HTML(tag:'h3', content: $heading);
			$this->username = new Render\HTML(tag:'p', attributes:['class'=>'gray'], content: $username);
	}
}

/*
<div id="focus">
    <div>
        <img width="64" src="https://noobscience.vercel.app/favicon.ico" alt="A Cool Image">
    </div>
    <div>
        <h3>{$encoded_data}</h3>
        <p class="gray">Administrator</p>
    </div>
</div>
*/