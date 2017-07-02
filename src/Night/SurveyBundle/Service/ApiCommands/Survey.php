<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 02.07.17
 * Time: 22:42
 */

namespace Night\SurveyBundle\Service\ApiCommands;


use Night\SurveyBundle\Entity\Form;
use Symfony\Component\Translation\TranslatorInterface;

class Survey extends AbstractApiService
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct($em, TranslatorInterface $translator)
    {
        parent::__construct($em);
        $this->translator = $translator;
    }

    public function getName()
    {
        return 'survey';
    }

    public function reorderForms($data)
    {
        foreach($data as $form) {
            $formEntity = $this->em->getRepository(Form::class)->findOneBy([
                'id' => $form['id']
            ]);
            $formEntity->setOrder($form['order']);
            $this->em->persist($formEntity);
        }
        $this->em->flush();
        return [
            'status' => 'OK',
            'message' => $this->translator->trans('administration.messages.change_order.ok', [], 'NightSurveyBundle')
        ];
    }
}