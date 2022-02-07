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

namespace OCA\TraqAvatar\Event;

use OCA\TraqAvatar\Handler\SyncUserAvatarHandler;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\User\Events\PostLoginEvent;

/**
 * This class listens for post login event.
 */
class PostLoginListener implements IEventListener
{
    /** @var SyncUserAvatarHandler */
    private SyncUserAvatarHandler $syncUserAvatarHandler;

    /**
     * PostLoginListener constructor.
     *
     * @param SyncUserAvatarHandler $syncUserAvatarHandler
     */
    public function __construct(SyncUserAvatarHandler $syncUserAvatarHandler)
    {
        $this->syncUserAvatarHandler = $syncUserAvatarHandler;
    }

    public function handle(Event $event): void
    {
        if (!($event instanceof PostLoginEvent)) {
            return;
        }
        $user = $event->getUser();
        if (empty($user)) {
            return;
        }
        $this->syncUserAvatarHandler->sync($user);
    }
}
