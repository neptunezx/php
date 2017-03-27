<?php


class StubTransport implements Requests_Transport {
    protected $method = "GET";

    /** @var Stub[] */
    protected $stubs = [];

    protected $scheme;
    protected $host;
    protected $path;
    protected $query;
    protected $body;

    public function request($url, $headers = array(), $data = array(), $options = array()) {
        $parsedUrl = parse_url($url);

        foreach ($this->stubs as $stub) {
            if ($stub->isPathMatch($parsedUrl["path"]) && $stub->isQueryMatch($parsedUrl['query'])) {
                $response = "HTTP/1.0 200 OK\r\n";
                $response .= "Content-Type: text/plain\r\n";
                $response .= "Connection: close\r\n\r\n";
                $response .= $stub->getBody();

                return $response;
            }
        }

        $response = "HTTP/1.0 530 No Stubs Matched\r\n";
        $response .= "Content-Type: text/plain\r\n";
        $response .= "Connection: close\r\n\r\n";

        $response .= "No stubs matched for request:\n";
        $response .= $parsedUrl['path'] . "?" . $parsedUrl['query'] . "\n\n";
        $response .= "Existing stubs:\n" . join("\n", $this->stubs) . "\n\n";

        return $response;
    }

    /**
     * @param string $path
     * @return Stub
     */
    public function stubFor($path)
    {
        $stub = new Stub($path);

        $this->stubs[] = $stub;

        return $stub;
    }

    public function request_multiple($requests, $options) {
        throw new Exception("Not implemented");
    }

    public static function test() {
        return true;
    }
}
