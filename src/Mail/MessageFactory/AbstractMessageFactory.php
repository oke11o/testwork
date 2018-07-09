<?php

namespace App\Mail\MessageFactory;

use App\Exception\Mail\UnavailableTypeException;
use App\Exception\Mail\MailException;
use App\Mail\MailMessageOptions;
use App\Mail\Type\AbstractMailType;
use Swift_Message;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractMessageFactory
{
    /**
     * @var EngineInterface
     */
    protected $renderer;
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    /**
     * @var MailMessageOptions
     */
    protected $options;
    /**
     * @var array
     */
    protected $templateParams;
    /**
     * @var array
     */
    protected $subjectTranslateParams;
    /**
     * @var AbstractMailType
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
        array $options
    ) {
        $this->renderer = $renderer;
        $this->translator = $translator;
        $this->options = new MailMessageOptions($options);
    }

    /**
     * @param AbstractMailType $type
     * @return Swift_Message $message
     * @throws \App\Exception\Mail\UnavailableTypeException
     */
    final public function create(AbstractMailType $type): Swift_Message
    {
        if (!$this->checkAvailableType($type)) {
            throw new UnavailableTypeException(self::class, $type);
        }
        $this->type = $type;

        $this->prepareTemplateParams();
        $this->prepareSubjectTranslateParams();

        return $this->createMessage();
    }

    /**
     * @param AbstractMailType $type
     * @return bool
     */
    abstract protected function checkAvailableType(AbstractMailType $type): bool;

    abstract protected function prepareTemplateParams();

    abstract protected function prepareSubjectTranslateParams();

    /**
     * @return Swift_Message $message
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function createMessage(): Swift_Message
    {
        $message = new Swift_Message();
        $this->setMessageSubject($message);
        $this->setMessageFrom($message);
        $this->setMessageBody($message);

        return $message;
    }


    /**
     * @param Swift_Message $message
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function setMessageSubject(Swift_Message $message): void
    {
        $subject = $this->translator->trans(
            $this->options->getSubject(),
            $this->getSubjectTranslateParams(),
            $this->options->getTranslationDomain(),
            $this->type->getLocale()
        );
        $message->setSubject($subject);
    }

    /**
     * @param Swift_Message $message
     * @throws \InvalidArgumentException
     */
    protected function setMessageFrom(Swift_Message $message): void
    {
        $fromName = $this->translator->trans(
            $this->options->getFromName(),
            [],
            $this->options->getTranslationDomain(),
            $this->type->getLocale()
        );
        $message->setFrom($this->options->getFromEmail(), $fromName);
    }

    /**
     * @param Swift_Message $message
     * @throws \RuntimeException
     */
    protected function setMessageBody(Swift_Message $message): void
    {
        $parameters = array_merge(
            [
                'locale' => $this->type->getLocale(),
                'translation_domain' => $this->options->getTranslationDomain(),
            ],
            $this->getTemplateParams()
        );

        $body = $this->renderer->render(
            $this->options->getTemplate(),
            $parameters
        );
        $message->setBody($body, 'text/html');
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    protected function getTemplateParams(): array
    {
        if ($this->templateParams === null) {
            throw new MailException('You need prepare $this->templateParams before!');
        }

        return $this->templateParams;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    protected function getSubjectTranslateParams(): array
    {
        if ($this->subjectTranslateParams === null) {
            throw new MailException('You need call prepare $this->subjectTranslateParams before!');
        }

        return $this->subjectTranslateParams;
    }

}