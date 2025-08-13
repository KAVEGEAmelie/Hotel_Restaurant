<?php
$user = App\Models\User::where('email', 'camillekvg99@gmail.com')->first();
echo 'User found: ' . ($user ? 'Yes' : 'No') . PHP_EOL;
if($user) {
    echo 'is_admin field: ' . ($user->is_admin ? 'true' : 'false') . PHP_EOL;
    echo 'isAdmin() method: ' . ($user->isAdmin() ? 'true' : 'false') . PHP_EOL;
    echo 'isGerant() method: ' . ($user->isGerant() ? 'true' : 'false') . PHP_EOL;
    echo 'canAccessAdmin() method: ' . ($user->canAccessAdmin() ? 'true' : 'false') . PHP_EOL;
    echo 'Email contains gerant: ' . (str_contains($user->email, 'gerant') ? 'true' : 'false') . PHP_EOL;
}
