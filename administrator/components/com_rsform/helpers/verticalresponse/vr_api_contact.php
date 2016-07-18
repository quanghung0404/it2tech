<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die;

// Let's load the required scripts for this file
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vr_api_client.php');

/**
 * Contact class that handles the calls and responses made to the VR API.
 */
class Contact extends VR_APIClient
{
	public function base_uri()
	{
		return ROOT_URL . 'contacts';
	}

	// Return all of your contacts wrapped in a Response object
	public function all($parameters = array())
	{
		$response = new Response(Contact::get(self::base_uri(), $parameters));
		self::handle_collection($response);

		return $response;
	}

	// Create a contact
	// Return the API response in a Contact object
	public function create($parameters = array())
	{
		$response = new Response(Contact::post(self::base_uri(), $parameters));

		return $response;
	}

	// Gets the details of a particular contact
	public function details($parameters = array())
	{
		// Let's make a request to get the details of the contact
		$response = self::get($this->response->url, $parameters);

		// Return the details as a contact object
		return new Contact($response);
	}

	// Instantiates the items of a collection response to a Contact object
	public function handle_collection($response)
	{
		foreach ($response->items as &$value)
		{
			$value = new Contact(new Response($value));
		}
	}
}
