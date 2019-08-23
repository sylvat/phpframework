<?php

namespace App\Utils;

/**
 * @Desc RSA加密
 * @Author tangqin
 * @Date 2016/8/2
 * @Time 16:10
 */
class Rsa
{
    /**
     * 生成公钥私钥
     * @param int $bits
     * @return array|bool
     */
    public static function create($bits = 1024)
    {
        $rsa = openssl_pkey_new(array('private_key_bits' => $bits, 'private_key_type' => OPENSSL_KEYTYPE_RSA));
        $n = $m = 0;
        while (($e = openssl_error_string()) !== false) {
            if ($n > 100) {
                return false;
            }
            $n++;
        }
        openssl_pkey_export($rsa, $privateKey);
        while (($e = openssl_error_string()) !== false) {
            if ($m > 100) {
                return false;
            }
            $m++;
        }
        $publicKey = openssl_pkey_get_details($rsa);
        $publicKey = $publicKey['key'];
        return [
            'privateKey' => $privateKey,
            'publicKey' => $publicKey
        ];
    }

    /**
     * 公匙加密
     * @param $sourceStr
     * @param $publicKey
     * @return bool
     */
    public static function publicKeyEncode($sourceStr, $publicKey)
    {

        $pubKeyId = openssl_get_publickey($publicKey);

        if (openssl_public_encrypt($sourceStr, $cryptText, $pubKeyId, OPENSSL_PKCS1_PADDING)) {
            return $cryptText;
        }
        return FALSE;
    }

    /**
     * 公匙解密
     * @param $cryptText
     * @param $publicKey
     * @return bool
     */
    public static function publicKeyDecode($cryptText, $publicKey)
    {
        $pubKeyId = openssl_get_publickey($publicKey);
        if (openssl_public_decrypt($cryptText, $sourceStr, $pubKeyId, OPENSSL_PKCS1_PADDING)) {
            return $sourceStr;
        }
        return FALSE;
    }

    /**
     * 私匙加密
     * @param $sourceStr
     * @param $privateKey
     * @return bool
     */
    public static function privateKeyEncode($sourceStr, $privateKey)
    {

        $priKeyId = openssl_get_privatekey($privateKey);
        if (openssl_private_encrypt($sourceStr, $cryptText, $priKeyId, OPENSSL_PKCS1_PADDING)) {
            return $cryptText;
        }
        return false;
    }

    /**
     * 私匙解密
     * @param $cryptText
     * @param $privateKey
     * @return bool
     */
    public static function privateKeyDecode($cryptText, $privateKey)
    {

        $priKeyId = openssl_get_privatekey($privateKey);
        if (openssl_private_decrypt($cryptText, $sourceStr, $priKeyId, OPENSSL_PKCS1_PADDING)) {
            return $sourceStr;
        }
        return false;
    }

    /**
     * 签名
     * @param $sourceStr
     * @param $privateKey
     * @return mixed
     */
    public static function sign($sourceStr, $privateKey)
    {
        $priKeyId = openssl_get_privatekey($privateKey);
        openssl_sign($sourceStr, $signature, $priKeyId);
        openssl_free_key($priKeyId);
        return $signature;

    }

    /**
     * 验证签名
     * @param $sourceStr
     * @param $signature
     * @param $publicKey
     * @return int
     */
    public static function verify($sourceStr, $signature, $publicKey)
    {
        $pubKeyId = openssl_get_publickey($publicKey);
        $verify = openssl_verify($sourceStr, $signature, $pubKeyId);
        openssl_free_key($pubKeyId);
        return $verify;

    }

    public static function makePublicKey($publicKey)
    {
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($publicKey, 64, "\n") . "-----END PUBLIC KEY-----";
        return $publicKey;

    }

    public static function makePrivateKey($privateKey)
    {
        $privateKey = "-----BEGIN PRIVATE KEY-----\n" . chunk_split($privateKey, 64, "\n") . "-----END PRIVATE KEY-----";
        return $privateKey;

    }

    public static function removePublicKey($publicKey)
    {
        $publicKey = str_replace("-----BEGIN PUBLIC KEY-----", "", $publicKey);
        $publicKey = str_replace("-----END PUBLIC KEY-----", "", $publicKey);
        $publicKey = str_replace("\n", "", $publicKey);
        $publicKey = str_replace("\r", "", $publicKey);
        return $publicKey;

    }

    public static function removePrivateKey($privateKey)
    {
        $privateKey = str_replace("-----BEGIN PRIVATE KEY-----", "", $privateKey);
        $privateKey = str_replace("-----END PRIVATE KEY-----", "", $privateKey);
        $privateKey = str_replace("\n", "", $privateKey);
        $privateKey = str_replace("\r", "", $privateKey);
        return $privateKey;

    }
}
