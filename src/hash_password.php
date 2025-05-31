<?php
$password_plain = 'dika';  // ganti sesuai password yang mau di-hash
$hash = password_hash($password_plain, PASSWORD_DEFAULT);
echo "Hash password: " . $hash;
