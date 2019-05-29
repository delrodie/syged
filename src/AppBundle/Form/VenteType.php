<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->album = $options['album'];
        $album = $this->album;

        $builder
            ->add('date', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>"La date de vente", 'data-provide'=>'datepicker', 'data-date-autoclose'=>'true', 'data-date-format'=>'yyyy/mm/dd']
            ])
            ->add('qte', IntegerType::class,[
                'attr'=>['class'=>'form-control', 'placeholder'=>"Quantité a vendre"]
            ])
            ->add('prix', IntegerType::class,[
                'attr'=> ['class'=> 'form-control', 'placeholder'=>"Prix unitaire de vente"]
            ])
            //->add('statut')->add('publiePar')->add('modifiePar')->add('publieLe')->add('modifieLe')
            ->add('album', EntityType::class,[
                'attr'=>['class'=>'form-control'],
                'class' =>'AppBundle:Album',
                'query_builder' => function(EntityRepository $repository) use ($album){
                    return $repository->findAlbums($album);
                },
                'choice_label' => 'titre'
            ])
            ->add('distributeur', null,[
                'attr'=>['class'=>'form-control select2'],
                'class' => 'AppBundle:Distributeur',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->liste();
                },
                'choice_label' => 'nom'
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Vente',
            'album' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_vente';
    }


}
