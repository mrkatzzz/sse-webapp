<?php
class GoogleAuthenticator {
    public function createSecret($secretLength = 16) {
        $validChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
        $secret = "";
        for ($i = 0; $i < $secretLength; $i++) {
            $secret .= $validChars[rand(0, 31)];
        }
        return $secret;
    }

    public function getCode($secret) {
        $time = floor(time() / 30);
        $base32chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
        $base32charsArray = array_flip(str_split($base32chars));
        
        $secretUpper = strtoupper($secret);
        $secretData = "";
        foreach (str_split($secretUpper) as $c) {
            $secretData .= str_pad(decbin($base32charsArray[$c]), 5, "0", STR_PAD_LEFT);
        }
        $secretData = str_split($secretData, 8);
        $binarySecret = "";
        foreach ($secretData as $bin) { $binarySecret .= chr(bindec($bin)); }

        $timeBinary = pack('N*', 0) . pack('N*', $time);
        $hash = hash_hmac('sha1', $timeBinary, $binarySecret, true);
        $offset = ord($hash[19]) & 0xf;
        $otp = (
            (ord($hash[$offset+0]) & 0x7f) << 24 |
            (ord($hash[$offset+1]) & 0xff) << 16 |
            (ord($hash[$offset+2]) & 0xff) << 8 |
            (ord($hash[$offset+3]) & 0xff)
        ) % 1000000;
        return str_pad($otp, 6, "0", STR_PAD_LEFT);
    }

    public function verifyCode($secret, $code) {
        return ($this->getCode($secret) == $code);
    }
}
?>
