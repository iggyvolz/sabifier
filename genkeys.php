<?php
$keyPair = sodium_crypto_sign_keypair();
?>
PUBLIC KEY:
<?= base64_encode(sodium_crypto_sign_publickey($keyPair)) ?>

PRIVATE KEY:
<?= base64_encode(sodium_crypto_sign_secretkey($keyPair)) ?>

