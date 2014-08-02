<?php
/**
 * Simple interface to AWS Security Token Service API
 *
 * @license MIT License
 * @author Tatsuya Tsuruoka <http://github.com/ttsuruoka>
 */
class SimpleAmazonSTS
{
	protected $access_key_id;
	protected $secret_access_key;
	protected $base_url = 'https://sts.amazonaws.com/';
	protected $version = '2011-06-15';

	public $status_code;
	public $raw_body;
	public $total_time;

	const USER_AGENT = 'SimpleAmazonSTS/1.0';

	public function __construct($access_key_id, $secret_access_key, $options = array())
	{
		$this->access_key_id = $access_key_id;
		$this->secret_access_key = $secret_access_key;
		foreach ($options as $k => $v) {
			$this->{$k} = $v;
		}
	}

	public static function urlencode($string)
	{
		// Encode URL according to RFC 3986
		return str_replace('%7E', '~', rawurlencode($string));
	}

	public function call($operation, $params = array())
	{
		$params['AWSAccessKeyId'] = $this->access_key_id;
		$params['Version'] = $this->version;
		$params['SignatureMethod'] = 'HmacSHA256';
		$params['SignatureVersion'] = 2;
		$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
		$params['Action'] = $operation;

		ksort($params);

		$canonical_string = array();
		foreach ($params as $k => $v) {
			$canonical_string[] = self::urlencode($k).'='.self::urlencode($v);
		}
		$canonical_string = join('&', $canonical_string);

		$parsed_url = parse_url($this->base_url);
		$string_to_sign = "POST\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
		$params['Signature'] = base64_encode(hash_hmac('sha256', $string_to_sign, $this->secret_access_key, true));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->base_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

		$this->raw_body = curl_exec($ch);
		$this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->total_time = round(curl_getinfo($ch, CURLINFO_TOTAL_TIME), 3);
		curl_close($ch);

		$xml = simplexml_load_string($this->raw_body);
		return json_decode(json_encode($xml), true);
	}
}