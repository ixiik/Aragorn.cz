<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette
 */



/**
 * The Nette Framework (http://nette.org)
 *
 * @author     David Grudl
 */
final class NFramework
{

	/** Nette Framework version identification */
	const NAME = 'Nette Framework',
		VERSION = '2.0-dev',
		REVISION = '539fdec released on 2011-04-13';

	/** @var bool set to TRUE if your host has disabled function ini_set */
	public static $iAmUsingBadHost = FALSE;



	/**
	 * Static class - cannot be instantiated.
	 */
	final public function __construct()
	{
		throw new LogicException("Cannot instantiate static class " . get_class($this));
	}

}

class NClosureFix
{
	static $vars = array();

	static function uses($args)
	{
		self::$vars[] = $args;
		return count(self::$vars)-1;
	}
}