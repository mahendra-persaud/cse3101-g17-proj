<?php
$users = json_decode(file_get_contents(__DIR__ . '/users.json'), true);
$u = $users[0];
echo password_verify('password123', $u['password']) ? 'OK' : 'FAIL';
