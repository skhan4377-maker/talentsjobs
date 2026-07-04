<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Jwt_lib {

    protected $CI;
    protected $key;
    protected $algo;
    protected $exp;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('jwt', TRUE);
        
        $this->key  = $this->CI->config->item('jwt_key', 'jwt');
        $this->algo = $this->CI->config->item('jwt_algorithm', 'jwt');
        $this->exp  = $this->CI->config->item('jwt_exp', 'jwt');
        
        log_message('debug', 'JWT Library initialized with key: ' . substr($this->key, 0, 10) . '...');
        log_message('debug', 'JWT Algorithm: ' . $this->algo);
        log_message('debug', 'JWT Expiry: ' . $this->exp . ' seconds');
    }

    // Generate Access Token
    public function generate_token($payload = []) {
        $now = time();
        
        $token = array_merge([
            'iat' => $now,
            'exp' => $now + (int)$this->exp
        ], $payload);

        log_message('debug', 'Generating token with payload: ' . json_encode($token));
        
        $jwt = JWT::encode($token, $this->key, $this->algo);
        
        log_message('debug', 'Generated token: ' . substr($jwt, 0, 50) . '...');
        
        return $jwt;
    }

    // Decode token
    public function decode_token($token) {
        try {
            log_message('debug', 'Decoding token: ' . substr($token, 0, 50) . '...');
            
            $decoded = JWT::decode($token, new Key($this->key, $this->algo));
            
            $array = (array)$decoded;
            log_message('debug', 'Token decoded successfully: ' . json_encode($array));
            
            // Check expiration
            if (isset($array['exp']) && $array['exp'] < time()) {
                log_message('debug', 'Token expired: exp=' . $array['exp'] . ', now=' . time());
                return false;
            }
            
            return $array;
        } catch (\Firebase\JWT\ExpiredException $e) {
            log_message('error', 'JWT Expired: ' . $e->getMessage());
            return false;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            log_message('error', 'JWT Signature Invalid: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            log_message('error', 'JWT Decode Error: ' . $e->getMessage());
            return false;
        }
    }

    // Generate Refresh Token
    public function generate_refresh_token() {
        return bin2hex(random_bytes(64));
    }
}