<?php

declare(strict_types=1);

namespace Infrangible\Whoops\Plugin\Magento\Framework\App;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\State;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Http
{
    /** @var State */
    protected $state;

    /** @var Run */
    protected $run;

    public function __construct(
        State $state,
        Run $run,
        PrettyPageHandler $prettyPageHandler
    ) {
        $this->state = $state;
        $this->run = $run;

        $this->run->pushHandler($prettyPageHandler);
    }

    /**
     * @throws \Exception
     * @noinspection PhpUnusedParameterInspection
     */
    public function aroundLaunch(\Magento\Framework\App\Http $subject, callable $proceed)
    {
        register_shutdown_function([$this, 'handleShutdown']);

        if ($this->state->getMode() === State::MODE_DEVELOPER) {
            try {
                return $proceed();
            } catch (\Throwable $throwable) {
                $this->run->handleException($throwable);
            }
        }

        return $proceed();
    }

    protected function handleShutdown()
    {
        $error = error_get_last();

        if (is_array($error)) {
            if ($this->state->getMode() === State::MODE_DEVELOPER) {
                if (Misc::isLevelFatal($error[ 'type' ]) && strpos(
                        $error[ 'message' ],
                        'Allowed memory size'
                    ) !== false) {

                    header('Content-Type: text/plain');
                    echo sprintf(
                        'PHP Fatal error: %s in %s on line %d',
                        $error[ 'message' ],
                        $error[ 'file' ],
                        $error[ 'line' ]
                    );
                } else {
                    $this->run->handleShutdown();
                }
            } else {
                echo 'An error has happened during application run. See PHP log for details.';
            }
        }
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function beforeCatchException(
        \Magento\Framework\App\Http $subject,
        Bootstrap $bootstrap,
        \Exception $exception
    ): array {
        if ($bootstrap->isDeveloperMode()) {
            $this->run->handleException($exception);
        }

        return [$bootstrap, $exception];
    }
}
