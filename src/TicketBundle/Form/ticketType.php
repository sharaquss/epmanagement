<?php

namespace TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use UserBundle\Entity\user;

class ticketType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('submit', SubmitType::class, array('label'=>'submit'))
     //       ->add('userCreated', 'text')
//            ->add('userAssigned', 'text')
     /*       ->add('dateCreation', 'datetime') */ //handled from within database
            ->add('dateDeadline', 'datetime')
            ->add('name', 'text')
            ->add('description', 'text')
            ->add('numberPoints', 'integer')
            ->add('project');
          //  ->add('status');
    }

    public function buildEditForm(FormBuilderInterface $builder, array $options) {
//        $builder
//            ->add('submit', SubmitType::class, array('label'=>'submit'))
//          // ->add('userCreated.username', 'text')
////            ->add('userAssigned', 'text')
//            /*       ->add('dateCreation', 'datetime') */ //handled from within database
//            ->add('dateDeadline', 'datetime')
//            ->add('name', 'text')
//            ->add('description', 'text')
//            ->add('numberPoints', 'integer')
//            ->add('project');
//         //   ->add('status');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TicketBundle\Entity\ticket'
        ));
    }
}
