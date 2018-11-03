<?php

namespace Clue\React\Buzz\Io;

use Clue\React\Buzz\Message\ResponseException;
use Clue\React\Buzz\Message\MessageFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Stream\ReadableStreamInterface;

/**
 * @internal
 */
class Transaction
{
    private $request;
    private $sender;
    private $messageFactory;

    private $numRequests = 0;

    // context: http.follow_location
    private $followRedirects = true;

    // context: http.max_redirects
    private $maxRedirects = 10;

    // context: http.ignore_errors
    private $obeySuccessCode = true;

    private $streaming = false;

    public function __construct(RequestInterface $request, Sender $sender, array $options = array(), MessageFactory $messageFactory)
    {
        foreach ($options as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }

        $this->request = $request;
        $this->sender = $sender;
        $this->messageFactory = $messageFactory;
    }

    public function send()
    {
        $deferred = new Deferred(function () use (&$deferred) {
            if (isset($deferred->pending)) {
                $deferred->pending->cancel();
                unset($deferred->pending);
            }
        });

        $this->next($this->request, $deferred)->then(
            array($deferred, 'resolve'),
            array($deferred, 'reject')
        );

        return $deferred->promise();
    }

    private function next(RequestInterface $request, Deferred $deferred)
    {
        $this->progress('request', array($request));

        $that = $this;
        ++$this->numRequests;

        $promise = $this->sender->send($request, $this->messageFactory);

        if (!$this->streaming) {
            $promise = $promise->then(function ($response) use ($deferred, $that) {
                return $that->bufferResponse($response, $deferred);
            });
        }

        $deferred->pending = $promise;

        return $promise->then(
            function (ResponseInterface $response) use ($request, $that, $deferred) {
                return $that->onResponse($response, $request, $deferred);
            }
        );
    }

    /**
     * @internal
     * @param ResponseInterface $response
     * @return PromiseInterface Promise<ResponseInterface, Exception>
     */
    public function bufferResponse(ResponseInterface $response, $deferred)
    {
        $stream = $response->getBody();

        // body is not streaming => already buffered
        if (!$stream instanceof ReadableStreamInterface) {
            return \React\Promise\resolve($response);
        }

        // buffer stream and resolve with buffered body
        $messageFactory = $this->messageFactory;
        $promise = \React\Promise\Stream\buffer($stream)->then(
            function ($body) use ($response, $messageFactory) {
                return $response->withBody($messageFactory->body($body));
            },
            function ($e) use ($stream) {
                // try to close stream if buffering fails (or is cancelled)
                $stream->close();

                throw $e;
            }
        );

        $deferred->pending = $promise;

        return $promise;
    }

    /**
     * @internal
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @throws ResponseException
     * @return ResponseInterface|PromiseInterface
     */
    public function onResponse(ResponseInterface $response, RequestInterface $request, $deferred)
    {
        $this->progress('response', array($response, $request));

        if ($this->followRedirects && ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400)) {
            return $this->onResponseRedirect($response, $request, $deferred);
        }

        // only status codes 200-399 are considered to be valid, reject otherwise
        if ($this->obeySuccessCode && ($response->getStatusCode() < 200 || $response->getStatusCode() >= 400)) {
            throw new ResponseException($response);
        }

        // resolve our initial promise
        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @return PromiseInterface
     * @throws \RuntimeException
     */
    private function onResponseRedirect(ResponseInterface $response, RequestInterface $request, $deferred)
    {
        // resolve location relative to last request URI
        $location = $this->messageFactory->uriRelative($request->getUri(), $response->getHeaderLine('Location'));

        $request = $this->makeRedirectRequest($request, $location);
        $this->progress('redirect', array($request));

        if ($this->numRequests >= $this->maxRedirects) {
            throw new \RuntimeException('Maximum number of redirects (' . $this->maxRedirects . ') exceeded');
        }

        return $this->next($request, $deferred);
    }

    /**
     * @param RequestInterface $request
     * @param UriInterface $location
     * @return RequestInterface
     */
    private function makeRedirectRequest(RequestInterface $request, UriInterface $location)
    {
        $originalHost = $request->getUri()->getHost();
        $request = $request
            ->withoutHeader('Host')
            ->withoutHeader('Content-Type')
            ->withoutHeader('Content-Length');

        // Remove authorization if changing hostnames (but not if just changing ports or protocols).
        if ($location->getHost() !== $originalHost) {
            $request = $request->withoutHeader('Authorization');
        }

        // naïve approach..
        $method = ($request->getMethod() === 'HEAD') ? 'HEAD' : 'GET';

        return $this->messageFactory->request($method, $location, $request->getHeaders());
    }

    private function progress($name, array $args = array())
    {
        return;

        echo $name;

        foreach ($args as $arg) {
            echo ' ';
            if ($arg instanceof ResponseInterface) {
                echo 'HTTP/' . $arg->getProtocolVersion() . ' ' . $arg->getStatusCode() . ' ' . $arg->getReasonPhrase();
            } elseif ($arg instanceof RequestInterface) {
                echo $arg->getMethod() . ' ' . $arg->getRequestTarget() . ' HTTP/' . $arg->getProtocolVersion();
            } else {
                echo $arg;
            }
        }

        echo PHP_EOL;
    }
}
