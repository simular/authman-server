<?php

declare(strict_types=1);

namespace App\Module\Front;

use Lyrasoft\Banner\BannerPackage;
use Lyrasoft\Contact\ContactPackage;
use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\Script\FontAwesomeScript;
use Lyrasoft\Luna\Services\ConfigService;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Unicorn\Script\UnicornScript;
use Unicorn\UnicornPackage;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Middleware\AbstractLifecycleMiddleware;
use Windwalker\DI\Exception\DefinitionException;

class FrontMiddleware extends AbstractLifecycleMiddleware
{
    use TranslatorTrait;

    public function __construct(
        protected AppContext $app,
        protected AssetService $asset,
        protected HtmlFrame $htmlFrame,
        protected UnicornScript $unicornScript,
        protected FontAwesomeScript $fontAwesomeScript,
    ) {
    }

    /**
     * prepareExecute
     *
     * @param  ServerRequestInterface  $request
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws DefinitionException
     */
    protected function preprocess(ServerRequestInterface $request): void
    {
        $this->lang->loadAllFromVendor(UnicornPackage::class, 'ini');
        $this->lang->loadAllFromVendor(LunaPackage::class, 'ini');
        $this->lang->loadAllFromVendor(ContactPackage::class, 'ini');
        $this->lang->loadAllFromVendor(BannerPackage::class, 'ini');

        $this->lang->loadAll('ini');

        // Unicorn
        $this->unicornScript->init('js/front/main.js');

        // Font Awesome
        $this->fontAwesomeScript->cssFont(
            FontAwesomeScript::PRO | FontAwesomeScript::DEFAULT_SET | FontAwesomeScript::LIGHT
        );

        // Bootstrap
        $this->asset->css('css/front/bootstrap.min.css');
        $this->asset->js('vendor/bootstrap/dist/js/bootstrap.bundle.min.js');

        // Main
        $this->asset->css('css/front/main.css');

        // Metadata
        $coreConfig = $this->app->service(ConfigService::class)->getConfig('core');

        $this->htmlFrame->setFavicon($this->asset->path('images/favicon.png'));
        $this->htmlFrame->setSiteName('Windwalker');
        $this->htmlFrame->setDescription('Windwalker Site Description.');
        // $this->htmlFrame->setCoverImages($this->asset->root('...'));

        if ($sc = trim((string) $coreConfig->get('google_search_console'))) {
            $this->htmlFrame->addMetadata('google-site-verification', $sc);
        }
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
