<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtisteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>"Nom de l'artiste"]
            ])
            ->add('statut', CheckboxType::class,[
                'attr'=>['class' => 'checkbox-tick'], 'required' => false
            ])
            //->add('albums')
            //->add('slug')->add('publiePar')->add('modifiePar')->add('publieLe')->add('modifieLe')
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Artiste'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_artiste';
    }


}
