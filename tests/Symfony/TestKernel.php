<?php


namespace Oesteve\Tests\Transformer\Symfony;

use Oesteve\Tests\Transformer\Dto\UserResolver;
use Oesteve\Transformer\Symfony\OesteveTransformerBundle;
use Oesteve\Transformer\Symfony\ResolverLocatorCompilerPass;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class TestKernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new OesteveTransformerBundle()
        ];
    }

    protected function configureContainer(ContainerConfigurator $c): void
    {
        // $c->import(__DIR__.'/config/framework.yaml');

        // register all classes in /src/ as service
        // $c->services()
        //    ->load('App\\', __DIR__.'/*')
        //    ->autowire()
        //    ->autoconfigure()
        // ;

        // configure WebProfilerBundle only if the bundle is enabled
        if (isset($this->bundles['WebProfilerBundle'])) {
            $c->extension('web_profiler', [
                'toolbar' => true,
                'intercept_redirects' => false,
            ]);
        }

        $c->services()
            ->set(UserResolver::class)
            ->tag(ResolverLocatorCompilerPass::RESOLVER_TAG)
            ->public();
    }

    // optional, to use the standard Symfony cache directory
    public function getCacheDir(): string
    {
        return __DIR__.'/../var/cache/'.$this->getEnvironment();
    }

    // optional, to use the standard Symfony logs directory
    public function getLogDir(): string
    {
        return __DIR__.'/../var/log';
    }
}
