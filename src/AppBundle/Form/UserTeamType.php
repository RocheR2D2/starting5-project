<?php

namespace AppBundle\Form;

use AppBundle\Entity\UserTeam;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTeamType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Name of team'))
            ->add('pointGuard', EntityType::class, array(
                'label' => 'Select Point Guard',
                'class' => 'AppBundle:Player',
                'choice_label' => 'name',
            ))
            ->add('shootingGuard', EntityType::class, array(
                'label' => 'Select Shooting Guard',
                'class' => 'AppBundle:Player',
                'choice_label' => 'name',
            ))
            ->add('powerForward', EntityType::class, array(
                'label' => 'Select Power Forward',
                'class' => 'AppBundle:Player',
                'choice_label' => 'name',
            ))
            ->add('smallForward', EntityType::class, array(
                'label' => 'Select Small Forward',
                'class' => 'AppBundle:Player',
                'choice_label' => 'name',
            ))
            ->add('center', EntityType::class, array(
                'label' => 'Select Center',
                'class' => 'AppBundle:Player',
                'choice_label' => 'name',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_token_id' => 'userTeam',
            // BC for SF < 2.8
            'intention' => 'userTeam',
            'data_class' => UserTeam::class
        ));
    }

    // BC for SF < 3.0
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user_team_new';
    }
}