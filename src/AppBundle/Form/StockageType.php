<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->artiste = $options['artiste'];
        $this->album = $options['album'];
        $artiste = $this->artiste;
        $album = $this->album;

        $builder
            ->add('date', TextType::class,[
              'attr'=>['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>"La date d'approvisionnement", 'data-provide'=>'datepicker', 'data-date-autoclose'=>'true', 'data-date-format'=>'yyyy/mm/dd']
            ])
            ->add('qte', IntegerType::class,[
              'attr'=>['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>"Quantité approvisionnée"]
            ])
            //->add('stockinitial')->add('publiePar')->add('modifiePar')->add('publieLe')->add('modifieLe')
            ->add('artiste', EntityType::class,[
              'attr'=>['class'=>'form-control'],
              'class'=>'AppBundle:Artiste',
              'query_builder'=> function(EntityRepository $repository) use($artiste){
                return $repository->findArtiste($artiste);
              },
              'choice_label' => 'nom'
            ])
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
            'data_class' => 'AppBundle\Entity\Stockage',
            'artiste' => null,
            'album' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_stockage';
    }


}
