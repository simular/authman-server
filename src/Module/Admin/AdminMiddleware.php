<?php

declare(strict_types=1);

namespace App\Module\Admin;

use Lyrasoft\Banner\BannerPackage;
use Lyrasoft\Contact\ContactPackage;
use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\Script\FontAwesomeScript;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Unicorn\Script\UnicornScript;
use Unicorn\UnicornPackage;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Middleware\AbstractLifecycleMiddleware;

class AdminMiddleware extends AbstractLifecycleMiddleware
{
    use TranslatorTrait;

    public function __construct(
        protected AppContext $app,
        protected AssetService $asset,
        protected UnicornScript $unicornScript,
        protected FontAwesomeScript $fontAwesomeScript,
        protected HtmlFrame $htmlFrame,
    ) {
    }

    /**
     * prepareExecute
     *
     * @param ServerRequestInterface $request
     *
     * @return  mixed
     */
    protected function preprocess(ServerRequestInterface $request): void
    {
        $this->lang->loadAllFromVendor(UnicornPackage::class, 'ini');
        $this->lang->loadAllFromVendor(LunaPackage::class, 'ini');
        $this->lang->loadAllFromVendor(ContactPackage::class, 'ini');
        $this->lang->loadAllFromVendor(BannerPackage::class, 'ini');

        $this->lang->loadAll('ini');

        // Unicorn
        $this->unicornScript->init('js/admin/main.js');

        // Font Awesome
        $this->fontAwesomeScript->cssFont(
            FontAwesomeScript::PRO | FontAwesomeScript::DEFAULT_SET | FontAwesomeScript::LIGHT
        );

        // Bootstrap
        $this->asset->css('css/admin/bootstrap.min.css');
        $this->asset->js('vendor/bootstrap/dist/js/bootstrap.bundle.min.js');

        // Theme
        $this->asset->js('vendor/jquery/dist/jquery.min.js');
        $this->asset->js('vendor/admin/metismenu/metisMenu.min.js');
        $this->asset->js('vendor/admin/simplebar/simplebar.min.js');
        $this->asset->js('vendor/admin/node-waves/waves.min.js');
        $this->asset->js('js/admin/app.min.js');
        $this->asset->css('css/admin/app.min.css');
        $this->asset->css('css/admin/icons.min.css');

        // Main
        $this->asset->css('css/admin/main.css');

        // Meta
        $this->htmlFrame->setFavicon($this->asset->path('images/admin/favicon.png'));
    }

    /**
     * postExecute
     *
     * @param ResponseInterface $response
     *
     * @return  mixed
     */
    protected function postProcess(ResponseInterface $response): void
    {
    }
}
