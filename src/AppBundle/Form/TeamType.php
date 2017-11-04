<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form;

use AppBundle\Entity\Team;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType'), array('label' => 'Name of Team', 'translation_domain' => 'FOSUserBundle'))
            ->add('slugTeam', TextType::class, array('label' => 'Slug of Team'))
            ->add('conference', EntityType::class, array(
                'label' => 'Select Conference',
                'class' => 'AppBundle:Conference',
                'choice_label' => 'name',
            ))
            ->add('division', EntityType::class, array(
                'label' => 'Select Division',
                'class' => 'AppBundle:Division',
                'choice_label' => 'name',
            ))
            ->add('state', EntityType::class, array(
                'label' => 'Select State',
                'class' => 'AppBundle:State',
                'choice_label' => 'stateName',
            ))
            ->add('town', EntityType::class, array(
                'label' => 'Select Town',
                'class' => 'AppBundle:Town',
                'choice_label' => 'name',
            ))
            ->add('stadium', EntityType::class, array(
                'label' => 'Select Stadium',
                'class' => 'AppBundle:Stadium',
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
            'csrf_token_id' => 'team',
            // BC for SF < 2.8
            'intention' => 'team',
            'data_class' => Team::class
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
        return 'app_team_edit';
    }
}