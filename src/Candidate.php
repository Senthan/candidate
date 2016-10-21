<?php
namespace Jeylabs\Candidate;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;

class Candidate
{
    const VERSION = '1.0.0';
    const POST_CANDIDATE_API = 'api/candidate';
    const POST_VACANCY_API = 'api/vacancy';
    const DEFAULT_TIMEOUT = 5;
    protected $client;
    protected $secret_key;
    protected $isAsyncRequest = false;
    protected $formParameters = [];
    protected $headers = [];
    protected $promises = [];
    protected $lastResponse;
    protected $candidateApiBabeUri;
    public function __construct($secret_key, $candidateApiBabeUri, $isAsyncRequest = false, $httpClient = null)
    {
        $this->secret_key = $secret_key;
        $this->candidateApiBabeUri = $candidateApiBabeUri;
        $this->isAsyncRequest = $isAsyncRequest;
        $this->client = $httpClient ?: new Client([
            'base_uri' => $this->candidateApiBabeUri,
            'timeout' => self::DEFAULT_TIMEOUT,
            'connect_timeout' => self::DEFAULT_TIMEOUT,
        ]);
    }
    public function isAsyncRequests()
    {
        return $this->isAsyncRequest;
    }
    public function setAsyncRequests($isAsyncRequest)
    {
        $this->isAsyncRequest = $isAsyncRequest;
        return $this;
    }
    public function getHeaders()
    {
        return $this->headers;
    }
    public function setHeaders($headers = [])
    {
        $this->headers = $headers;
        return $this;
    }
    public function getFormParameter()
    {
        return $this->formParameters;
    }
    public function setFormParameter($formParameters = [])
    {
        $this->formParameters = $formParameters;
        return $this;
    }
    public function getLastResponse()
    {
        return $this->lastResponse;
    }
    protected function makeRequest($method, $uri, $query = [], $formParameters = [], $file = null)
    {
        $options[GuzzleRequestOptions::FORM_PARAMS] = $formParameters;
        $options[GuzzleRequestOptions::QUERY] = $query;
        $options[GuzzleRequestOptions::HEADERS] = $this->getDefaultHeaders();

        if(isset($file)) {
            $options['file'] = $file;
        }


        if ($this->isAsyncRequest) {
            return $this->promises[] = $this->client->requestAsync($method, $uri, $options);
        }
        $this->lastResponse = $this->client->request($method, $uri, $options);
        return json_decode($this->lastResponse->getBody(), true);
    }
    protected function getDefaultHeaders()
    {
        return array_merge([
            'Authorization' => 'Bearer ' . $this->secret_key,
        ], $this->headers);
    }
    protected function getDefaultFormParameter() {

        return array_merge([
            'Authorization' => 'Bearer ' . $this->secret_key,
        ], $this->formParameters);
    }
    public function saveCandidate($query = [], $formParameters = [], $file = null) {

        $this->setHeaders(['Content-type' => 'application/json']);
        $uri = self::POST_CANDIDATE_API;
        return $this->makeRequest('POST', $uri, $query, $formParameters, $file);
    }

    public function saveVacancy($query = [], $formParameters = [], $file = null) {

        $this->setHeaders(['Content-type' => 'application/json']);
        $uri = self::POST_VACANCY_API;
        return $this->makeRequest('POST', $uri, $query, $formParameters, $file);
    }
    public function __destruct()
    {
        Promise\unwrap($this->promises);
    }
}