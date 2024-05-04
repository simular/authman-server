<?php

declare(strict_types=1);

namespace App\Module\Api;

use App\Attributes\Transaction;
use App\DTO\UserDTO;
use App\Entity\Account;
use App\Repository\AccountRepository;
use Unicorn\Flysystem\Base64DataUri;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Security\Exception\UnauthorizedException;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\Filesystem\FileObject;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\StrNormalize;

use function Windwalker\fs;
use function Windwalker\Query\uuid2bin;

#[Controller]
class AccountController
{
    public function items(
        AppContext $app,
        #[Autowire]
        AccountRepository $repository,
        \CurrentUser $currentUser
    ): array {
        $q = (string) $app->input('q');

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
            ->limit(0)
            ->page(1)
            ->all(Account::class);

        $user = UserDTO::wrap($currentUser);

        return compact(
            'user',
            'items',
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
        \CurrentUser $currentUser
    ): Account {
        $item = $app->input('item');

        $account = $orm->toEntity(Account::class, $item);

        if ($account->getUserId()->toString() !== $currentUser->getId()->toString()) {
            throw new UnauthorizedException('Invalid user ID');
        }

        $id = $account->getId();

        $current = $orm->findOne(Account::class, $id);

        if ($current) {
            $orm->updateOne(Account::class, $account);
        } else {
            $account = $orm->createOne(Account::class, $account);
        }

        return $account;
    }

    #[Transaction]
    public function saveMultiple(
        AppContext $app,
        ORM $orm,
        \CurrentUser $currentUser
    ): array {
        $items = $app->input('items');

        $accounts = [];

        foreach ($items as $item) {
            $account = $orm->toEntity(Account::class, $item);

            if ($account->getUserId()->toString() !== $currentUser->getId()->toString()) {
                throw new UnauthorizedException('Invalid user ID');
            }

            $accounts[] = $account;
        }

        foreach ($accounts as $i => $account) {
            $id = $account->getId();

            $current = $orm->findOne(Account::class, $id);

            if ($current) {
                $orm->updateOne(Account::class, $account);
            } else {
                $account = $orm->createOne(Account::class, $account);
            }

            $accounts[$i] = $accounts;
        }

        return $accounts;
    }

    #[Transaction]
    public function delete(AppContext $app, ORM $orm, \CurrentUser $currentUser): true
    {
        $ids = (array) $app->input('ids');

        if (!$ids) {
            throw new \RuntimeException('No IDs');
        }

        $ids = array_map(fn($id) => (string) uuid2bin($id), $ids);

        $accounts = $orm->findList(Account::class, ['id' => $ids ?: [0]])->all();

        /** @var Account $account */
        foreach ($accounts as $account) {
            if ($account->getUserId()->toString() !== $currentUser->getId()->toString()) {
                throw new UnauthorizedException('Invalid user ID');
            }

            $orm->deleteWhere(Account::class, ['id' => $account->getId()->getBytes()]);
        }

        return true;
    }
}
