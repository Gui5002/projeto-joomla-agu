<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Site\Service;

use Joomla\CMS\Factory;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Routing class from com_guestsupport
 *
 * @since  3.3
 */
class Router extends RouterView
{
    /**
     * Flag to remove IDs
     *
     * @var    boolean
     */
    protected $noIDs = false;

    /**
     * Content Component router constructor
     *
     * @param   SiteApplication           $app              The application object
     * @param   AbstractMenu              $menu             The menu object to work with
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu)
    {
		/* $params = Factory::getApplication()->getParams('com_guestsupport');
		$this->noIDs = (bool) $params->get('sef_ids'); */

		$tickets = new RouterViewConfiguration('tickets');
		$this->registerView($tickets);
		$ccTicket = new RouterViewConfiguration('ticket');
		$ccTicket->setKey('id')->setParent($tickets);
		$this->registerView($ccTicket);

		// Create ticket page
        $createticket = new RouterViewConfiguration('createticket');
        $createticket->setKey('id');
        $this->registerView($createticket);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
    }

	public function getTicketSegment($id, $query)
	{
		return array($id => $id);
	}
	public function getTicketId($segment, $query)
	{
		return $segment;
	}

	/**
	 * Create ticket menu
	 */
    public function getCreateticketId($segment, $query)
    {
        return (int) $segment;
    }
    public function getCreateticketSegment($id, $query)
    {
        return [$id => (int) $id];
    }
}
