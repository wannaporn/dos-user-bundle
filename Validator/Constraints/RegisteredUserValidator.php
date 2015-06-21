<?php

namespace DoS\UserBundle\Validator\Constraints;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RegisteredUserValidator extends ConstraintValidator
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @param RepositoryInterface $customerRepository
     */
    public function __construct(RepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($customer, Constraint $constraint)
    {
        $existingCustomer = $this->customerRepository->findOneBy(array('email' => $customer->getEmail()));

        if (null !== $existingCustomer && null !== $existingCustomer->getUser()) {
            $this->context->addViolationAt('email', $constraint->message, array(), null);
        }

        $existingCustomer = $this->customerRepository->findOneBy(array('mobile' => $customer->getMobile()));

        if (null !== $existingCustomer && null !== $existingCustomer->getUser()) {
            $this->context->addViolationAt('mobile', $constraint->message, array(), null);
        }
    }
}
