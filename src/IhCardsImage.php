<?php

namespace Infohoundru\Cards;

/**
 * Class IhCards
 * @package IhCards
 *
 * @example IhCards::stream('upload', $token, 'fieldName');
 *          (new IhCards)->stream('upload', $token, 'fieldName);
 *
 */
class IhCardsImage
{
    /**
     * File from $_FILES[$field].
     *
     * @var array
     */
    protected $file;

    /**
     * Types that can be loaded.
     *
     * @var array
     */
    protected $types;

    /**
     * Access token.
     *
     * @var string
     */
    protected $token;

    /**
     * Method action.
     *
     * @var string
     */
    protected $action;

    /**
     * Connect address.
     */
    const TCP = 'http://api.cards.infohound.ru/';

    /**
     * IhCardsImage constructor.
     *
     * @param string $action
     * @param string $token
     * @param mixed $field
     */
    public function __construct($action, $token, $field = null)
    {
        if (isset($_FILES[$field])) {
            $this->file = $_FILES[$field];
            $this->types = ['image/jpeg'];
        }

        $this->token = $token;
        $this->action = $action;
    }

    /**
     * Check uploaded file.
     *
     * @return bool
     * @throws \Exception
     */
    private function isUploaded()
    {
        if (isset($this->file)) {
            if (0 === $this->file['error']) {
                if (in_array($this->file['type'], $this->types)) {
                    return true;
                }

                throw new \Exception('Incorrect file type. File type must be a ' .
                    implode(', ', $this->types) . '.');
            }

            throw new \Exception('File error - "' . $this->file['error'] . '"');
        }

        throw new \Exception('Empty file');
    }

    /**
     * Start sending and return response.
     *
     * @return bool|mixed
     * @throws \Exception
     */
    protected function stream()
    {
        if ('upload' === $this->action) {
            if (true !== $this->isUploaded()) {
                return false;
            }
        }

        $curl = curl_init();

        $this->setOpt($curl);
        $response = $this->send($curl);
        $this->checkError($curl);

        curl_close($curl);

        return $response;

    }

    /**
     * Setup curl options.
     *
     * @param $curl
     */
    private function setOpt($curl)
    {
        curl_setopt($curl, CURLOPT_URL, self::TCP . $this->action);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getRequestHeaders());
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            curl_setopt($curl, CURLOPT_POST, true);

            if ('upload' === $this->action) {
                $file = new \CURLFile($this->file['tmp_name'], $this->file['type'], $this->file['name']);
                curl_setopt($curl, CURLOPT_POSTFIELDS, ['file' => $file]);
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_POST));
            }
        }
    }

    /**
     * Get request headers in array.
     *
     * @return array
     */
    private function getRequestHeaders()
    {
        $headers = [];
        $headers[] = 'Accept:application/json';
        $headers[] = 'Authorization:Bearer ' . $this->token;

        foreach ($_SERVER as $key => $value) {
            if (false !== strpos($key, 'HTTP')) {
                if ('HTTP_HOST' !== $key &&
                    'HTTP_ORIGIN' !== $key &&
                    'HTTP_CONTENT_TYPE' !== $key &&
                    false === strpos($key, 'HTTP_CONTENT_LEN')) {
                    $key = str_replace(' ', '-',
                        ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    array_push($headers, $key . ': ' . $value);
                }
            }
        }

        return $headers;
    }

    /**
     * Perform a cURL session.
     *
     * @param $curl
     * @return mixed
     */
    private function send($curl)
    {
        return curl_exec($curl);
    }

    /**
     * Check errors.
     *
     * @param $curl
     * @return bool
     * @throws \Exception
     */
    private function checkError($curl)
    {
        $err = curl_errno($curl);

        if ($err) {
            throw new \Exception(curl_strerror($err), $err);
        }

        return false;
    }

    /**
     * Allows call '(new Class)->method' on protected methods.
     *
     * @param $name
     * @param $arguments
     * @return bool
     */
    public function __call($name, $arguments)
    {
        $methods = (new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PROTECTED);

        foreach ($methods as $method) {
            if ($method->name === $name) {
                $method->setAccessible(true);
                return $method->invoke($this);
            }
        }

        return false;
    }

    /**
     * Allows use 'Class::method()' on protected methods.
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $class = new self(...$arguments);

        return $class->$method();
    }
}
