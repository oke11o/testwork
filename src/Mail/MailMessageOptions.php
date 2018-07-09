<?php

namespace App\Mail;

use Symfony\Component\OptionsResolver\OptionsResolver;

class MailMessageOptions
{
    private $options;

    /**
     * MessageOptions constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(['from_email', 'from_name', 'subject', 'translation_domain', 'template'])
            ->setRequired(['from_email', 'from_name', 'subject'])
            ->setDefaults(
                [
                    'translation_domain' => 'mail',
                    'template' => ':mail:template.html.twig',
                ]
            );

        $types = [
            'from_email' => 'string',
            'from_name' => 'string',
            'subject' => 'string',
            'template' => 'string',
            'translation_domain' => 'string',
        ];

        foreach ($types as $key => $type) {
            $resolver->setAllowedTypes($key, $type);
        }
    }

    public function getFromEmail()
    {
        return $this->options['from_email'];
    }

    public function getFromName()
    {
        return $this->options['from_name'];
    }

    public function getSubject()
    {
        return $this->options['subject'];
    }

    public function getTranslationDomain()
    {
        return $this->options['translation_domain'];
    }

    public function getTemplate()
    {
        return $this->options['template'];
    }

}