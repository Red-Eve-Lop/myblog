<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create', function () {
    $name = $this->ask('名前');
    $email = $this->ask('メールアドレス');
    $password = $this->secret('パスワード（12文字以上）');
    $confirmation = $this->secret('パスワード（確認）');

    $validator = Validator::make([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $confirmation,
    ], [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:12', 'confirmed'],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return Command::FAILURE;
    }

    $user = new User;
    $user->name = $name;
    $user->email = $email;
    $user->password = $password;
    $user->is_admin = true;
    $user->save();

    $this->info('管理者を作成しました。');

    return Command::SUCCESS;
})->purpose('Create an administrator');
