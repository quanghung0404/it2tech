<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die;

/**
 * Exception class for any unsuccessfull VR API calls
 * For more information, please refer to the Exception class in the PHP documentation
 */
class VR_API_Error extends \Exception
{
	private $url = null;
	private $params = null;
	private $failures = null;
	private $method;

	// Redefine the exception so message isn't optional
	public function __construct($response, Exception $previous = null)
	{
		// Let's store the values of the response that cause the API error
		if (array_key_exists('url', $response))
		{
			$this->url = $response['url'];
		}
		if (array_key_exists('params', $response))
		{
			$this->params = $response['params'];
		}
		if (array_key_exists('failures', $response))
		{
			$this->failures = $response['failures'];
		}
		$this->method = $response['method'];
		$message      = $response['message'];
		$code         = $response['code'];
		// Make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}

	// Return the string representation of the exception
	public function __toString()
	{
		return __CLASS__ . "{$this->method} request: [{$this->code}]: {$this->message}\n
			URL: ({$this->method}) {$this->url}\n
			Parameters: " . print_r($this->params) . "\n
			Failures: " . print_r($this->failures);
	}

	// Returns the URL the request was made to
	public function getURL()
	{
		return $this->url;
	}

	// Returns the method used to perform the request
	public function getMethod()
	{
		return $this->method;
	}

	// Returns the parameters used in the request
	public function getParameters()
	{
		return $this->params;
	}

	// Returns the failures returned by the API
	public function getFailures()
	{
		return $this->failures;
	}

}

/**
 * Exception class for cURL errors
 * For more information, please refer to the Exception class in the PHP documentation
 */
class CURL_Error extends \Exception
{
}
