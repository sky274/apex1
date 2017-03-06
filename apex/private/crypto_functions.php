<?php
// Symmetric Encryption
// Cipher method to use for symmetric encryption
const AES_256 ='AES-256-CBC'; 
const AES_128 ='AES-128-CBC'; 
const AES_192 ='AES-192-CBC'; 
const DES = "DES-EDE3-CBC"; 
const BF ='BF-CBC'; 
function key_length($cipher_method){
    return 32;
}

function key_encrypt($message, $key, $cipher_method=AES_256) {
  
  $length = key_length($cipher_method);
  
  $key = str_pad($key, $length, '*');
  $iv_length = openssl_cipher_iv_length($cipher_method);
  $iv = openssl_random_pseudo_bytes($iv_length);
  $encrypted = openssl_encrypt($message, $cipher_method, $key, OPENSSL_RAW_DATA, $iv);
  $encrypted_message = $iv . $encrypted;
  return base64_encode($encrypted_message);
}

function key_decrypt($string, $key, $cipher_method=AES_256) {
  $length = key_length($cipher_method);
  $key = str_pad($key, $length, '*');
  $iv_with_ciphertext = base64_decode($string);
  $iv_length = openssl_cipher_iv_length($cipher_method);
  $iv = substr($iv_with_ciphertext, 0, $iv_length);
  $ciphertext = substr($iv_with_ciphertext, $iv_length);
  $plaintext = openssl_decrypt($ciphertext, $cipher_method, $key, OPENSSL_RAW_DATA, $iv);
  return $plaintext;
}
// Asymmetric Encryption / Public-Key Cryptography
// Cipher configuration to use for asymmetric encryption
const PUBLIC_KEY_CONFIG = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

function generate_keys($config=PUBLIC_KEY_CONFIG) {
  $resource = openssl_pkey_new($config);
  openssl_pkey_export($resource, $private_key);
  $key_details = openssl_pkey_get_details($resource);
  $public_key = $key_details["key"];
  return array('private' => $private_key, 'public' => $public_key);
}

function pkey_encrypt($string, $public_key) {
  openssl_public_encrypt($string, $encrypted_message, $public_key);
  return base64_encode($encrypted_message); 
}

function pkey_decrypt($string, $private_key) {
  openssl_private_decrypt(base64_decode($string), $decrypted_message, $private_key);
  return $decrypted_message; 
}

function create_signature($data, $private_key) {
  openssl_sign($data, $raw_signature, $private_key);
  return base64_encode($raw_signature);
}

function verify_signature($data, $signature, $public_key) {
  $raw_signature = base64_decode($signature);
  $raw_result = openssl_verify($data, $raw_signature, $public_key);
  return $raw_result;
}
?>

