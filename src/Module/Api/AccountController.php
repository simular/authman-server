<?php

declare(strict_types=1);

namespace App\Module\Api;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Lyrasoft\Luna\User\UserService;
use Unicorn\Flysystem\Base64DataUri;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;

use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Filesystem;
use Windwalker\Filesystem\TempFileObject;
use Windwalker\Http\HttpClient;
use Windwalker\Utilities\StrNormalize;

use function Windwalker\fs;
use function Windwalker\Query\uuid2bin;
use function Windwalker\uid;

#[Controller]
class AccountController
{
    public function items(
        AppContext $app,
        #[Autowire]
        AccountRepository $repository,
        \CurrentUser $currentUser
    ): array {
        [
            $q,
            $page
        ] = $app->input('q', 'page');

        $q = (string) $q;
        $page = min(1, (int) $page);

        $items = $repository->getApiListSelector()
            ->searchTextFor(
                $q,
                [
                    'account.title',
                    'account.url'
                ]
            )
            ->where('user_id', uuid2bin($currentUser->getId()))
            ->order('account.created', 'DESC')
            ->page($page)
            ->all(Account::class);

        return compact(
            'items'
        );
    }

    public function logoSearch(AppContext $app): array
    {
        $q = (string) $app->input('q');
        $color = (string) $app->input('color');

        $dir = fs(WINDWALKER_ROOT . '/node_modules/@fortawesome/fontawesome-free/svgs');

        $searchText = strtolower(StrNormalize::toKebabCase($q));

        $directFile = $dir->appendPath('/solid/' . $searchText . '.svg');

        if ($directFile->exists()) {
            $image = $this->readImageIcon($directFile, $color, $app);

            return compact('image');
        }

        foreach ($dir->files(true) as $file) {
            $basename = 'fa-' . $file->getFilename();

            if (str_contains($basename, $searchText)) {
                $image = $this->readImageIcon($file, $color, $app);

                return compact('image');
            }
        }

        return [];
    }

    protected function readImageIcon(FileObject $file, string $color, AppContext $app): string
    {
        $svg = (string) $file->read();
        $svg = str_replace('<path ', "<path fill=\"$color\" ", $svg);

        $uid = uid();

        $tmp = new TempFileObject(WINDWALKER_TEMP . '/' . $uid . '.svg');
        $tmp->deleteWhenDestruct();
        $tmp->deleteWhenShutdown();
        $tmp->write($svg);

        $png = new TempFileObject(WINDWALKER_TEMP . '/' . $uid . '.png');

        $cmd = sprintf(
            '%s  -background none -resize 96x96 -filter catrom -colors 16 %s %s',
            env('IMAGICK_CLI') ?: 'convert',
            $tmp->getPathname(),
            $png->getPathname()
        );
        $process = $app->runProcess($cmd);

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $image = $png->readBase64DataUri('image/png');

        $png->delete();
        $tmp->delete();

        return $image;
}
}
