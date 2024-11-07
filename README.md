# Croute Symfony Bundle

This package integrates the [Croute Router](https://github.com/thewunder/croute) 
into Symfony, and makes it available as a service.

Install using:

```shell
composer require thewunder/croute-bundle
```

Configure your controller namespace(s) in config/packages/croute.yaml

```yaml
croute:
    namespaces:
      - App\Controller
```

In your Kernel.php return the CrouteKernel instead of the default symfony Router.

```php
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getHttpKernel(): HttpKernelInterface
    {
        return  $this->getContainer()->get('croute.kernel');
    }
}
```

And all controllers in the configured namespace(s) and set up for autowiring.

```php
namespace App\Controller

use Croute\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function __construct(private readonly LoggerInterface $orm)
    {
    }
    
    public function indexAction(): Response 
    {
        $this->logger->debug('IndexController->indexAction');
        return new Response('Hello from Croute!');
    }
}

```
