<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ExcelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('excel_file', FileType::class, [
                'label' => 'Envoyer un fichier excel',
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '8196k',
                        'mimeTypes' => [
                            "application/vnd.ms-excel",
                            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                            "application/vnd.ms-office",
                            "application/x-excel",
                            "application/x-msexcel",
                            "application/x-dos_ms_excel",
                            "application/xls",
                            "application/x-xls",
                            "application/xlsx",
                            "application/vnd.ms-excel.sheet.macroEnabled.12",
                            "application/vnd.ms-excel.template.macroEnabled.12",
                            "application/vnd.ms-excel.addin.macroEnabled.12",
                            "application/vnd.ms-excel.sheet.binary.macroEnabled.12",
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être au format Excel',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
