<?php
declare(strict_types=1);

/**
 * @copyright Copyright (c) 2018 Michael Weimann <mail@michael-weimann.eu>
 *
 * @author Michael Weimann <mail@michael-weimann.eu>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\TraqAvatar\AppInfo;

use OCA\TraqAvatar\Avatar\AvatarService;
use OCA\TraqAvatar\Avatar\TraqAvatarService;
use OCA\TraqAvatar\Event\PostLoginListener;
use OCA\TraqAvatar\Handler\DirectUpdateSyncUserAvatarHandler;
use OCA\TraqAvatar\Handler\SyncUserAvatarHandler;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Http\Client\IClientService;
use OCP\IAvatarManager;
use OCP\User\Events\PostLoginEvent;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * The app.
 */
class Application extends App implements IBootstrap
{

    const APP_ID = 'TraqAvatar';

    /**
     * Application constructor.
     * Registers the app's services.
     *
     * @param array $urlParams
     */
    public function __construct(array $urlParams = array())
    {
        parent::__construct(self::APP_ID, $urlParams);

        // OCP event dispatcher (recommended as of OC 23)
        // https://docs.nextcloud.com/server/latest/developer_manual/basics/events.html
        /** @var IEventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get(IEventDispatcher::class);
        $dispatcher->addServiceListener(PostLoginEvent::class, PostLoginListener::class);
    }

    public function register(IRegistrationContext $context): void
    {
        $context->registerService(AvatarService::class, function (ContainerInterface $c) {
            return new TraqAvatarService(
                $c->get(IClientService::class)->newClient(),
                $c->get(LoggerInterface::class)
            );
        });

        $context->registerService(SyncUserAvatarHandler::class, function (ContainerInterface $c) {
            return new DirectUpdateSyncUserAvatarHandler(
                $c->get(AvatarService::class),
                $c->get(IAvatarManager::class)
            );
        });

        $context->registerService(PostLoginListener::class, function (ContainerInterface $c) {
            return new PostLoginListener(
                $c->get(SyncUserAvatarHandler::class)
            );
        });
    }

    public function boot(IBootContext $context): void
    {
        // no-op
    }
}
