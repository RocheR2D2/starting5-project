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

use AppBundle\Entity\Player;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array('label' => 'Firstname of Player'))
            ->add('lastname', TextType::class, array('label' => 'Lastname of Player'))
            ->add('slugPlayer', TextType::class, array('label' => 'Slug of Player'))
            ->add('shirtNumber', NumberType::class, array('label' => '# of Player'))
            ->add('nbaDebut', NumberType::class, array('label' => 'NBA Debut'))
            ->add('born', BirthdayType::class, array('label' => 'Date of Birth'))
            ->add('fGPercentage', NumberType::class, array('label' => 'FG%'))
            ->add('threePointsPercentage', NumberType::class, array('label' => '3P%'))
            ->add('fTPercentage', NumberType::class, array('label' => 'FT%'))
            ->add('PPG', NumberType::class, array('label' => 'PPG'))
            ->add('RPG', NumberType::class, array('label' => 'RPG'))
            ->add('APG', NumberType::class, array('label' => 'APG'))
            ->add('BPG', NumberType::class, array('label' => 'BPG'))
            ->add('height', NumberType::class, array('label' => 'Height'))
            ->add('weight', NumberType::class, array('label' => 'Weight'))
            ->add('team', EntityType::class, array(
                'label' => 'Select Team',
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
            ))
            ->add('position', EntityType::class, array(
                'label' => 'Select Position',
                'class' => 'AppBundle:Position',
                'choice_label' => 'name',
            ))
            ->add('state', EntityType::class, array(
                'label' => 'Select Native State of Player',
                'class' => 'AppBundle:State',
                'choice_label' => 'stateName',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_token_id' => 'player',
            // BC for SF < 2.8
            'intention' => 'player',
            'data_class' => Player::class
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
        return 'app_player_edit';
    }
}