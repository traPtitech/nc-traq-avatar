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

namespace OCA\TraqAvatar\Avatar;

use Exception;
use OC\AppFramework\Http;
use OC_Image;
use OCP\Http\Client\IClient;
use OCP\IImage;
use OCP\IUser;
use Psr\Log\LoggerInterface;

/**
 * Traq avatar service implementation using traQ to query the avatar.
 */
class TraqAvatarService implements AvatarService
{

    const TRAQ_AVATAR_URL = 'https://q.trap.jp/api/v3/public/icon/%s';

    /**
     * @var IClient
     */
    private IClient $httpClient;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * TraqAvatarService constructor.
     *
     * @param IClient $httpClient
     * @param LoggerInterface $logger
     */
    public function __construct(IClient $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * Retrieves the user's avatar from traQ.
     *
     * @param IUser $user
     * @return null|IImage
     */
    public function query(IUser $user)
    {
        $id = $user->getUID();
        if (!empty($id)) {
            $response = $this->fetchFromTraq($id);
        } else {
            $this->logger->info("User ID empty");
            $response = null;
        }
        return $response;
    }

    /**
     * Fetches avatar from traQ.
     *
     * @param string $id The traQ ID to retrieve avatar for.
     * @return null|IImage The avatar as image or null if none found.
     */
    private function fetchFromTraq(string $id)
    {
        $requestUrl = $this->buildTraqRequestUrl($id);

        try {
            $response = $this->httpClient->get($requestUrl);
        } catch (Exception $e) {
            return null;
        }

        $avatar = null;

        if ($response->getStatusCode() === Http::STATUS_OK) {
            $avatarData = $response->getBody();
            $avatarImage = new OC_Image();
            if ($avatarImage->loadFromData($avatarData)) {
                $avatar = $avatarImage;
            } else {
                $this->logger->error("Failed to load image from data");
            }
        }


        return $avatar;
    }

    /**
     * Builds the traQ avatar request url.
     *
     * @param string $traqId The traQ ID
     * @return string
     */
    private function buildTraqRequestUrl(string $traqId): string
    {
        return sprintf(self::TRAQ_AVATAR_URL, $traqId);
    }
}
