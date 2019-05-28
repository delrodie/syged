<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StickageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->album = $options['album'];
        $album = $this->album;

        $builder
            ->add('qte', IntegerType::class,[
                'attr'=>['class'=>'form-control', 'placeholder'=>"Quantité a sticker"]
            ])
            //->add('stockinitial')
            ->add('date', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>"La date de stickage", 'data-provide'=>'datepicker', 'data-date-autoclose'=>'true', 'data-date-format'=>'yyyy/mm/dd']
            ])
            //->add('publiePar')->add('modifiePar')->add('publieLe')->add('modifieLe')
            ->add('album', EntityType::class,[
                'attr'=>['class'=>'form-control show-tick ms select2'],
                'class' =>'AppBundle:Album',
                'query_builder' => function(EntityRepository $repository) use ($album){
                    return $repository->findAlbums($album);
                },
                'choice_label' => 'titre'
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Stickage',
            'album' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_stickage';
    }


}
