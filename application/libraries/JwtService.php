<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JwtService
{
    protected $CI;
    protected $secret;
    protected $algo;
    protected $issuer;
    protected $audience;
    protected $ttl;
    protected $leeway;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('jwt', TRUE);

        $this->secret   = (string) $this->CI->config->item('jwt_secret', 'jwt');
        $this->algo     = (string) $this->CI->config->item('jwt_algo', 'jwt');
        $this->issuer   = (string) $this->CI->config->item('jwt_issuer', 'jwt');
        $this->audience = (string) $this->CI->config->item('jwt_audience', 'jwt');
        $this->ttl      = (int) $this->CI->config->item('jwt_ttl', 'jwt');
        $this->leeway   = (int) $this->CI->config->item('jwt_leeway', 'jwt');

        if ($this->secret === '' || $this->algo !== 'HS256') {
            throw new RuntimeException('JWT is not configured correctly.');
        }
    }

    public function encode(array $claims, $ttl = NULL)
    {
        $now = time();
        $ttl = $ttl === NULL ? $this->ttl : (int) $ttl;

        $payload = array_merge([
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttl,
            'jti' => bin2hex(random_bytes(16)),
        ], $claims);

        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $segments = [
            $this->b64url_encode(json_encode($header)),
            $this->b64url_encode(json_encode($payload)),
        ];
        $signing_input = implode('.', $segments);
        $segments[] = $this->b64url_encode($this->sign($signing_input));

        return implode('.', $segments);
    }

    public function decode($token)
    {
        if ( ! is_string($token) || substr_count($token, '.') !== 2) {
            return NULL;
        }

        list($header64, $payload64, $signature64) = explode('.', $token);

        $header = json_decode($this->b64url_decode($header64), TRUE);
        $payload = json_decode($this->b64url_decode($payload64), TRUE);
        $signature = $this->b64url_decode($signature64);

        if ( ! is_array($header) || ! is_array($payload) || $signature === FALSE) {
            return NULL;
        }

        if ( ! isset($header['alg']) || $header['alg'] !== 'HS256') {
            return NULL;
        }

        $expected = $this->sign($header64.'.'.$payload64);

        if ( ! hash_equals($expected, $signature)) {
            return NULL;
        }

        $now = time();

        if (isset($payload['nbf']) && ($now + $this->leeway) < (int) $payload['nbf']) {
            return NULL;
        }

        if (isset($payload['exp']) && ($now - $this->leeway) >= (int) $payload['exp']) {
            return NULL;
        }

        if (isset($payload['iss']) && $payload['iss'] !== $this->issuer) {
            return NULL;
        }

        if (isset($payload['aud']) && $payload['aud'] !== $this->audience) {
            return NULL;
        }

        return $payload;
    }

    public function ttl()
    {
        return $this->ttl;
    }

    protected function sign($data)
    {
        return hash_hmac('sha256', $data, $this->secret, TRUE);
    }

    protected function b64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function b64url_decode($data)
    {
        $remainder = strlen($data) % 4;

        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }
}
