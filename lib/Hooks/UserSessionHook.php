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

namespace OCA\TraqAvatar\Hooks;

use OCA\TraqAvatar\Handler\SyncUserAvatarHandler;
use OCP\IRequest;
use OCP\IUserSession;

/**
 * This class handles user session hooks.
 */
class UserSessionHook {
    /** @var IRequest */
	private IRequest $request;
	/** @var IUserSession */
	private IUserSession $session;
	/** @var SyncUserAvatarHandler */
	private SyncUserAvatarHandler $syncUserAvatarHandler;

	/**
	 * UserSessionHook constructor.
	 *
     * @param IRequest $request
     * @param IUserSession $session
	 * @param SyncUserAvatarHandler $syncUserAvatarHandler
	 */
	public function __construct(IRequest $request,
                                IUserSession $session,
                                SyncUserAvatarHandler $syncUserAvatarHandler) {
	    $this->request = $request;
        $this->session = $session;
		$this->syncUserAvatarHandler = $syncUserAvatarHandler;
	}

    public function handle(): void {
	    if (strpos($this->request->getRequestUri(), "/index.php/login") !== 0) {
	        return;
        }
	    if (!$this->session->isLoggedIn()) {
	        return;
        }
	    $user = $this->session->getUser();
        if (empty($user)) {
            return;
        }
        $this->syncUserAvatarHandler->sync($user);
    }
}
