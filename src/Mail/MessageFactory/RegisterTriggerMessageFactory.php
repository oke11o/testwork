<?php

namespace App\Mail\MessageFactory;

use App\Mail\Type\AbstractMailType;
use App\Mail\Type\RegisterTriggerMailType;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RegisterTriggerMessageFactory extends AbstractMessageFactory
{
    /**
     * @var RegisterTriggerMailType
     */
    protected $type;

    /**
     * @param EngineInterface $renderer
     * @param TranslatorInterface $translator
     * @param array $options
     */
    public function __construct(
        EngineInterface $renderer,
        TranslatorInterface $translator,
        array $options = []
    ) {
        $options = array_merge(
            [
                'template' => 'mail/user/trigger.html.twig',
                'subject' => 'user.trigger.subject',
                'translation_domain' => 'mail',
            ],
            $options
        );
        parent::__construct($renderer, $translator, $options);
    }

    /**
     * @param AbstractMailType $type
     * @return bool
     */
    protected function checkAvailableType(AbstractMailType $type): bool
    {
        return $type instanceof RegisterTriggerMailType;
    }

    protected function prepareTemplateParams()
    {
        $this->templateParams =
            [
                'email' => $this->type->getEmail(),
                'username' => $this->type->getUsername(),
            ];
    }

    protected function prepareSubjectTranslateParams()
    {
        $this->subjectTranslateParams = ['%username%' => $this->type->getUsername()];
    }
}