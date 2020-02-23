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
use OCA\TraqAvatar\Handler\DirectUpdateSyncUserAvatarHandler;
use OCA\TraqAvatar\Handler\SyncUserAvatarHandler;
use OCA\TraqAvatar\Hooks\UserSessionHook;
use \OCP\AppFramework\App;

/**
 * The app.
 */
class Application extends App {

	const APP_ID = 'TraqAvatar';

	/**
	 * Application constructor.
	 * Registers the app's services.
	 *
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams=array()) {
		parent::__construct(self::APP_ID, $urlParams);

		$container = $this->getContainer();
		$server = $container->getServer();

		$container->registerService(AvatarService::class, function() use ($server) {
			$httpClient = $server->getHTTPClientService()->newClient();
			return new TraqAvatarService($httpClient, $server->getLogger());
		});

		$container->registerService(DirectUpdateSyncUserAvatarHandler::class, function() use ($server, $container) {
			$avatarService = $container->query(AvatarService::class);
			$avatarManager = $server->getAvatarManager();
			return new DirectUpdateSyncUserAvatarHandler($avatarService, $avatarManager);
		});

		$container->registerService(SyncUserAvatarHandler::class, function() use ($container, $server) {
            return $container->query(DirectUpdateSyncUserAvatarHandler::class);
		});

		$container->registerService(UserSessionHook::class, function() use ($server, $container) {
			$userSession = $server->getUserSession();
			$syncUserAvatarHandler = $container->query(SyncUserAvatarHandler::class);
			return new UserSessionHook($userSession, $syncUserAvatarHandler);
		});
	}
}
