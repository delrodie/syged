<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistributeurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'attr'=>['class' => 'form-control', 'placeholder'=>"Le nom ou structure du distributeur", 'autocomplete'=>'off']
            ])
            ->add('situation', TextType::class,[
                'attr'=>['class' => 'form-control', 'placeholder'=>"La situation géographique", 'autocomplete'=>'off']
            ])
            ->add('contact', TextType::class,[
                'attr'=>['class' => 'form-control', 'placeholder'=>"Le contact telephonique", 'autocomplete'=>'off'], 'required'=> false
            ])
           /* ->add('structure', TextType::class,[
                'attr'=>['class' => 'form-control', 'placeholder'=>"La structure"], 'required'=> false
            ])*/
            ->add('statut', CheckboxType::class,[
                'attr'=>['class' => 'checkbox-tick'], 'required' => false
            ])
            //->add('slug')->add('publiePar')->add('modifiePar')->add('publieLe')->add('modifieLe')
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Distributeur'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_distributeur';
    }


}
