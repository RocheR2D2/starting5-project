<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class QuizzType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question',TextType::class, array('required'=>true))
            ->add('answer1',TextType::class, array('required'=>true))
            ->add('answer2',TextType::class, array('required'=>true))
            ->add('answer3',TextType::class, array('required'=>true))
            ->add('answer4',TextType::class, array('required'=>true))
            ->add('QCM_answer',ChoiceType::class, array(
                'choices' => array(
                    "answer1" => 1,
                    "answer2" => 2,
                    "answer3" => 3,
                    "answer4" => 4
                ),
                'required'=>true,
                'expanded' => true,
                'multiple' => false,
                'data' => 1
            ))
            ->add('type',ChoiceType::class, array(
                'choices' => array(
                    'QCM' => 'QCM',
                    'Question' => 'Question'
                )
            ))
            ->add('save', SubmitType::class, array('label' => 'Ajouter'));;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Quizz'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_quizz';
    }


}
