<?php

namespace Laravel\Socialite\Two;

class GitlabProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        if (is_null($this->instance_uri))
            return $this->buildAuthUrlFromBase('https://gitlab.com/oauth/authorize', $state);
        else {
            return $this->buildAuthUrlFromBase($this->instance_uri . '/oauth/authorize', $state);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        if (is_null($this->instance_uri))
            return 'https://gitlab.com/oauth/token';
        else
            return $this->instance_uri.'/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        if (is_null($this->instance_uri))
            $userUrl = 'https://gitlab.com/api/v3/user?access_token='.$token;
        else
            $userUrl = $this->instance_uri.'/api/v3/user?access_token='.$token;

        $response = $this->getHttpClient()->get($userUrl);

        $user = json_decode($response->getBody(), true);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['avatar_url'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }
}
