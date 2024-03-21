<?php

declare(strict_types=1);

namespace App\Module\Api;

use App\Attributes\Transaction;
use App\Entity\Account;
use App\Repository\AccountRepository;
use App\Service\AccountService;
use App\Service\EncryptionService;
use Unicorn\Flysystem\Base64DataUri;
use Unicorn\Upload\FileUploadManager;
use Unicorn\Upload\FileUploadService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\DI\Attributes\Service;
use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Path;
use Windwalker\Filesystem\TempFileObject;
use Windwalker\Http\Response\AttachmentResponse;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\StrNormalize;

use function Windwalker\fs;
use function Windwalker\Query\uuid2bin;
use function Windwalker\response;
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
            $page,
        ] = $app->input('q', 'page');

        $q = (string) $q;
        $page = min(1, (int) $page);

        $items = $repository->getApiListSelector()
            ->searchTextFor(
                $q,
                [
                    'account.title',
                    'account.url',
                ]
            )
            ->where('user_id', uuid2bin($currentUser->getId()))
            ->order('account.created', 'DESC')
            ->page($page)
            ->all(Account::class);

        $testImage = (string) $app->getNav()->to('api_v1_test_image')
            ->full();

        /** @var Account $item */
        foreach ($items as $item) {
            if ($item->getImage() === 'dev://test-image') {
                $item->setImage($testImage);
            }
        }

        return compact(
            'items'
        );
    }

    public function logoSearch(AppContext $app): array
    {
        $q = (string) $app->input('q') ?: 'key';
        $color = (string) $app->input('color') ?: '#ffffff';

        $dir = fs(WINDWALKER_ROOT . '/node_modules/@fortawesome/fontawesome-free/svgs');

        $searchText = strtolower(StrNormalize::toKebabCase($q));

        $directFile = $dir->appendPath('/solid/' . $searchText . '.svg');

        if ($directFile->exists()) {
            $image = $this->readImageIcon($directFile, $color, $app);
            $icon = $directFile->getBasename('.svg');

            return compact('image', 'icon');
        }

        $directFile = $dir->appendPath('/brands/' . $searchText . '.svg');

        if ($directFile->exists()) {
            $image = $this->readImageIcon($directFile, $color, $app);
            $icon = $directFile->getBasename('.svg');

            return compact('image', 'icon');
        }

        foreach ($dir->files(true) as $file) {
            $basename = 'fa-' . $file->getFilename();

            if (str_contains($basename, $searchText)) {
                $image = $this->readImageIcon($file, $color, $app);
                $icon = $file->getBasename('.svg');

                return compact('image', 'icon');
            }
        }

        return [
            'image' => '',
            'icon' => '',
        ];
    }

    protected function readImageIcon(FileObject $file, string $color, AppContext $app): string
    {
        $svg = (string) $file->read();
        $svg = str_replace('<path ', "<path fill=\"$color\" ", $svg);
        //
        // $uid = uid();
        //
        // $tmp = new TempFileObject(WINDWALKER_TEMP . '/' . $uid . '.svg');
        // $tmp->deleteWhenDestruct();
        // $tmp->deleteWhenShutdown();
        // $tmp->write($svg);
        //
        // $png = new TempFileObject(WINDWALKER_TEMP . '/' . $uid . '.png');
        //
        // $cmd = sprintf(
        //     '"%s"  -background none -resize 96x96 -filter catrom -colors 16 %s %s',
        //     env('IMAGICK_CLI') ?: 'convert',
        //     $tmp->getPathname(),
        //     $png->getPathname()
        // );
        // $process = $app->runProcess($cmd);
        //
        // if (!$process->isSuccessful()) {
        //     throw new \RuntimeException($process->getErrorOutput());
        // }
        //
        // $image = $png->readBase64DataUri('image/png');
        //
        // $png->delete();
        // $tmp->delete();

        return Base64DataUri::encode($svg, 'image/svg+xml');
    }

    #[Transaction]
    public function save(
        AppContext $app,
        ORM $orm,
        #[Service(FileUploadManager::class, 'default')]
        FileUploadService $fileUploadService
    ): Account {
        $item = $app->input('item');
        $image = $app->input('image');

        $account = $orm->toEntity(Account::class, $item);

        $result = $fileUploadService->handleFileData(
            $image,
            'logo/' . (string) $account->getId() . '.png'
        );
        $image = (string) $result?->getUri();

        if (!Path::isAbsolute($image)) {
            $systemUri = $app->getSystemUri();
            $image = $systemUri->addUriBase($image, $systemUri->root);
        }

        $account->setImage($image);

        $account = $orm->createOne(Account::class, $account);

        return $account;
    }

    public function testImage(AccountService $accountService): AttachmentResponse
    {
        return response()
            ->attachment()
            ->withFileData($accountService->getTestImage(), 'text/plain');
    }
}
