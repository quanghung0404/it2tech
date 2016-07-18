<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die;

// Let's load the required scripts for this file
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vr_api_contact.php');

/**
 * List class that handles the calls and responses made to the VR API.
 */
class ContactList extends VR_APIClient
{
	public function base_uri()
	{
		return ROOT_URL . 'lists/';
	}

	public function contacts_uri($list_id)
	{
		return self::base_uri() . $list_id . '/contacts/';
	}

	// Return all of your lists wrapped in a Response object
	public function all($parameters = array())
	{
		$response = new Response(ContactList::get(self::base_uri(), $parameters));
		self::handle_collection($response);

		return $response;
	}

	// Create a new list
	// Return the API response in a Contact object
	public function create($parameters = array())
	{
		$response = new Response(ContactList::post(self::base_uri(), $parameters));

		return $response;
	}

	// Gets the details of a particular list
	public function details($parameters = array())
	{
		// Let's make a request to get the details of the list
		$response = self::get($this->response->url, $parameters);

		// Return the details as a list object
		return new ContactList($response);
	}

	// Create a contact for the list with the parameters provided
	public function create_contact($parameters = array())
	{
		$url      = self::contacts_uri(self::id());
		$response = new Response(ContactList::post($url, $parameters));

		return $response;
	}

	// Returns all the contacts that belong to the list
	public function contacts($parameters = array())
	{
		$url      = self::contacts_uri(self::id());
		$response = new Response(ContactList::get($url, $parameters));
		foreach ($response->items as &$value)
		{
			$value = new Contact(new Response($value));
		}

		return $response;
	}

	// Instantiates the items of a collection response to a List object
	public function handle_collection($response)
	{
		foreach ($response->items as &$value)
		{
			$value = new ContactList(new Response($value));
		}
	}
}
