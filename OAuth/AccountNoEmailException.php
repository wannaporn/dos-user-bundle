<?php

namespace DoS\UserBundle\OAuth;

use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;

class AccountNoEmailException extends AccountNotLinkedException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Not found email to completed registration. ' .
        'Check setting on your {{ resource_owner_name }} '.
        'to provide an email with API access.';
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageData()
    {
        return array('{{ resource_owner_name }}' => ucfirst($this->getResourceOwnerName()));
    }
}
