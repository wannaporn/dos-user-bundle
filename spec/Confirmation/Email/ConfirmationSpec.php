<?php

namespace spec\DoS\UserBundle\Confirmation\Email;

use Doctrine\Common\Persistence\ObjectManager;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use DoS\UserBundle\Confirmation\SenderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Storage\StorageInterface;
use Sylius\Component\User\Security\TokenProviderInterface;

class ConfirmationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('DoS\UserBundle\Confirmation\Email\Confirmation');
    }

    function let(
        ObjectManager $manager,
        SenderInterface $sender,
        StorageInterface $storage,
        TokenProviderInterface $tokenProvider
    ) {
        $this->beConstructedWith($manager, $sender, $storage, $tokenProvider);
    }

    function it_be_email_type()
    {
        $this->getType()->shouldBe('email');
    }

    function it_can_send_token_to_user_for_confirmation_via_email(
        ConfirmationSubjectInterface $subject,
        TokenProviderInterface $tokenProvider
    ) {
        $tokenProvider->generateUniqueToken()->shouldBeCalled()->willReturn('foobar');
        $subject->setConfirmationType('email')->shouldBeCalled();
        $subject->confirmationRequest('foobar')->shouldBeCalled();
        $subject->getConfirmationChannel('customer.email')->shouldBeCalled()->willReturn('dos@gmail.com');
        $subject->getConfirmationToken()->shouldBeCalled()->willReturn('foobar');

        $this->send($subject);
    }

    function it_cannot_send_token_when_email_was_not_valid(
        ConfirmationSubjectInterface $subject,
        TokenProviderInterface $tokenProvider
    ) {
        $tokenProvider->generateUniqueToken()->shouldBeCalled()->willReturn('foobar');
        $subject->setConfirmationType('email')->shouldBeCalled();
        $subject->confirmationRequest('foobar')->shouldBeCalled();
        $subject->getConfirmationChannel('customer.email')->shouldBeCalled()->willReturn('a_invalid_email');

        $this->shouldThrow('DoS\UserBundle\Confirmation\Exception\NotFoundChannelException')->duringSend($subject);
    }
}
