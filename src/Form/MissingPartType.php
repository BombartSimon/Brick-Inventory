<?php

namespace App\Form;

use App\Entity\MissingPart;
use App\Entity\Set;
use App\Entity\SetPart;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissingPartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $set = $options['set'] ?? null;
        
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Missing Quantity',
                'data' => 1,
                'attr' => [
                    'min' => 1,
                    'class' => 'form-control'
                ]
            ]);

        // If a set is provided, filter parts for this set only
        if ($set instanceof Set) {
            $builder->add('part', EntityType::class, [
                'class' => SetPart::class,
                'choice_label' => function (SetPart $setPart) {
                    return sprintf('%s - %s (qty: %d)', 
                        $setPart->getPartNum(), 
                        $setPart->getPartName(), 
                        $setPart->getQuantity()
                    );
                },
                'choices' => $set->getSetParts(),
                'label' => 'Set Part',
                'placeholder' => 'Select a part...',
                'attr' => ['class' => 'form-control']
            ]);
        } else {
            $builder->add('part', EntityType::class, [
                'class' => SetPart::class,
                'choice_label' => function (SetPart $setPart) {
                    return sprintf('%s - %s (Set: %s)', 
                        $setPart->getPartNum(), 
                        $setPart->getPartName(),
                        $setPart->getSet()?->getName() ?? 'N/A'
                    );
                },
                'label' => 'Part',
                'placeholder' => 'Select a part...',
                'attr' => ['class' => 'form-control']
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MissingPart::class,
            'set' => null,
        ]);
        
        $resolver->setAllowedTypes('set', ['null', Set::class]);
    }
}
