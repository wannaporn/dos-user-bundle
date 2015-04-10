<?php

namespace Dos\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Dos\ResourceBundle\Form\DataTransformer\IdentifierToObjectTransformer;
use Dos\UserBundle\Doctrine\ORM\UserRepository;

class UserSearchType extends AbstractType
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $route;

    /**
     * {@inheritdoc}
     */
    public function __construct(UserRepository $repository, RouterInterface $router, $route)
    {
        $this->repository = $repository;
        $this->router = $router;
        $this->route = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new IdentifierToObjectTransformer($this->repository, $options['identifier'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['selectize'] = $options['selectize'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required' => true,
            'identifier' => 'username',
            'selectize' => array(
                'plugins' => array(),
                'valueField' => 'username',
                'labelField' => 'displayname',
                'searchField' => ['username', 'displayname'],
                'tpl' => 'tpl-user-selectize',
                'url' => $this->router->generate($this->route, array('query' => ':query')),
            ),
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_user_search';
    }
}
