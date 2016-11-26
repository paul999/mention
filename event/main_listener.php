<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paul999\mention\event;

/**
 * @ignore
 */
use phpbb\controller\helper;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * phpBB mentions Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/* @var helper */
	protected $helper;

	/* @var template */
	protected $template;

	/**
	 * Constructor
	 *
	 * @param helper	$helper		Controller helper object
	 * @param template	$template	Template object
	 */
	public function __construct(helper $helper, template $template)
	{
		$this->helper = $helper;
		$this->template = $template;
	}

    static public function getSubscribedEvents()
    {
        return [

        ];
    }
}
