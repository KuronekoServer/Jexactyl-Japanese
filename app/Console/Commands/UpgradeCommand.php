<?php

namespace Pterodactyl\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Pterodactyl\Console\Kernel;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Helper\ProgressBar;

class UpgradeCommand extends Command
{
    protected const DEFAULT_URL = 'https://github.com/jexactyl/jexactyl/releases/%s/panel.tar.gz';

    protected $signature = 'p:upgrade
        {--user= : PHP が実行されるユーザー。すべてのファイルの所有者はこのユーザーになります。}
        {--group= : PHP が動作するグループ。すべてのファイルの所有者はこのグループになります。}
        {--url= : ダウンロードするアーカイブを指定します。}
        {--release= : GitHubからダウンロードする特定のバージョン。最新版を使用する場合は空白にしてください。}
        {--skip-download : この場合、アーカイブはダウンロードされません。}';

    protected $description = 'JexactylのGitHubから新しいアーカイブをダウンロードし、アップグレードコマンドを実行します。';

    /**
     * Executes an upgrade command which will run through all of our standard
     * commands for Jexactyl and enable users to basically just download
     * the archive and execute this and be done.
     *
     * This places the application in maintenance mode as well while the commands
     * are being executed.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $skipDownload = $this->option('skip-download');
        if (!$skipDownload) {
            $this->output->warning('このコマンドは、ダウンロードしたアセットの完全性を確認するものではありません。ダウンロードを続ける前に、ダウンロード元が信頼できることを確認してください。アーカイブをダウンロードしたくない場合は、-skip-download フラグを使用するか、以下の質問に「no」と答えてください。');
            $this->output->comment('ダウンロード元 (set with --url=):');
            $this->line($this->getUrl());
        }

        if (version_compare(PHP_VERSION, '8.0') < 0) {
            $this->error('セルフアップグレード処理を実行できません。必要なPHPの最低バージョンは8.0です。 [' . PHP_VERSION . '].');
        }

        $user = 'www-data';
        $group = 'www-data';
        if ($this->input->isInteractive()) {
            if (!$skipDownload) {
                $skipDownload = !$this->confirm('最新版のアーカイブファイルをダウンロードし、解凍してみませんか？', true);
            }

            if (is_null($this->option('user'))) {
                $userDetails = posix_getpwuid(fileowner('public'));
                $user = $userDetails['name'] ?? 'www-data';

                if (!$this->confirm("Your webserver user has been detected as <fg=blue>[{$user}]:</> is this correct?", true)) {
                    $user = $this->anticipate(
                        'webサーバプロセスを実行しているユーザー名を入力してください。これはシステムによって異なりますが、一般的には "www-data", "nginx", または "apache" です。',
                        [
                            'www-data',
                            'nginx',
                            'apache',
                        ]
                    );
                }
            }

            if (is_null($this->option('group'))) {
                $groupDetails = posix_getgrgid(filegroup('public'));
                $group = $groupDetails['name'] ?? 'www-data';

                if (!$this->confirm("Your webserver group has been detected as <fg=blue>[{$group}]:</> is this correct?", true)) {
                    $group = $this->anticipate(
                        'ウェブサーバプロセスを実行しているグループ名を入力してください。通常、これはあなたのユーザーと同じです。',
                        [
                            'www-data',
                            'nginx',
                            'apache',
                        ]
                    );
                }
            }

            if (!$this->confirm('Panel のアップグレードプロセスを実行することは間違いないですか？')) {
                $this->warn('ユーザーによってアップグレードプロセスが終了されました。');

                return;
            }
        }

        ini_set('output_buffering', '0');
        $bar = $this->output->createProgressBar($skipDownload ? 9 : 10);
        $bar->start();

        if (!$skipDownload) {
            $this->withProgress($bar, function () {
                $this->line("\$upgrader> curl -L \"{$this->getUrl()}\" | tar -xzv");
                $process = Process::fromShellCommandline("curl -L \"{$this->getUrl()}\" | tar -xzv");
                $process->run(function ($type, $buffer) {
                    $this->{$type === Process::ERR ? 'error' : 'line'}($buffer);
                });
            });
        }

        $this->withProgress($bar, function () {
            $this->line('$upgrader> php artisan down');
            $this->call('down');
        });

        $this->withProgress($bar, function () {
            $this->line('$upgrader> chmod -R 755 storage bootstrap/cache');
            $process = new Process(['chmod', '-R', '755', 'storage', 'bootstrap/cache']);
            $process->run(function ($type, $buffer) {
                $this->{$type === Process::ERR ? 'error' : 'line'}($buffer);
            });
        });

        $this->withProgress($bar, function () {
            $command = ['composer', 'install', '--no-ansi'];
            if (config('app.env') === 'production' && !config('app.debug')) {
                $command[] = '--optimize-autoloader';
                $command[] = '--no-dev';
            }

            $this->line('$upgrader> ' . implode(' ', $command));
            $process = new Process($command);
            $process->setTimeout(10 * 60);
            $process->run(function ($type, $buffer) {
                $this->line($buffer);
            });
        });

        /** @var \Illuminate\Foundation\Application $app */
        $app = require __DIR__ . '/../../../bootstrap/app.php';
        /** @var \Pterodactyl\Console\Kernel $kernel */
        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();
        $this->setLaravel($app);

        $this->withProgress($bar, function () {
            $this->line('$upgrader> php artisan view:clear');
            $this->call('view:clear');
        });

        $this->withProgress($bar, function () {
            $this->line('$upgrader> php artisan config:clear');
            $this->call('config:clear');
        });

        $this->withProgress($bar, function () {
            $this->line('$upgrader> php artisan migrate --force --seed');
            $this->call('migrate', ['--force' => true, '--seed' => true]);
        });

        $this->withProgress($bar, function () use ($user, $group) {
            $this->line("\$upgrader> chown -R {$user}:{$group} *");
            $process = Process::fromShellCommandline("chown -R {$user}:{$group} *", $this->getLaravel()->basePath());
            $process->setTimeout(10 * 60);
            $process->run(function ($type, $buffer) {
                $this->{$type === Process::ERR ? 'error' : 'line'}($buffer);
            });
        });

        $this->withProgress($bar, function () {
            $this->line('$upgrader> php artisan queue:restart');
            $this->call('queue:restart');
        });

        $this->withProgress($bar, function () {
            $this->line('$upgrader> php artisan up');
            $this->call('up');
        });

        $this->newLine(2);
        $this->info('Panel は正常にアップグレードされました。Wings インスタンスも更新してください。: https://pterodactyl.io/wings/1.0/upgrading.html');
    }

    protected function withProgress(ProgressBar $bar, Closure $callback)
    {
        $bar->clear();
        $callback();
        $bar->advance();
        $bar->display();
    }

    protected function getUrl(): string
    {
        if ($this->option('url')) {
            return $this->option('url');
        }

        return sprintf(self::DEFAULT_URL, $this->option('release') ? 'download/v' . $this->option('release') : 'latest/download');
    }
}
