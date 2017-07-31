<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 15.01.17
 * Time: 19:11
 */

namespace Night\SurveyBundle\DTO;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class SurveyDTO
{
    /**
     * @var string
     */
    private $surveyId;

    /**
     * @var string
     */
    private $formId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $subTitle;

    /**
     * @var integer
     */
    private $currentPage;

    /**
     * @var integer
     */
    private $maxPage;

    /**
     * @var string
     */
    private $formText;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var string
     */
    private $resultText;

    /**
     * SurveyDTO constructor.
     *
     * @param               $surveyId
     * @param               $formId
     * @param string        $title
     * @param string        $subTitle
     * @param int           $currentPage
     * @param int           $maxPage
     * @param string        $formText
     * @param FormInterface $form
     * @param               $resultText
     */
    public function __construct(
        $surveyId,
        $formId,
        $title,
        $subTitle,
        $currentPage,
        $maxPage,
        $formText,
        FormInterface $form = null,
        $resultText
    ) {
        $this->surveyId = $surveyId;
        $this->formId = $formId;
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->currentPage = $currentPage;
        $this->maxPage = $maxPage;
        $this->formText = $formText;
        $this->form = $form;
        $this->resultText = $resultText;
    }

    /**
     * @return string
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getMaxPage()
    {
        return $this->maxPage;
    }

    /**
     * @return string
     */
    public function getFormText()
    {
        return $this->formText;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    public function getFormView()
    {
        return $this->form->createView();
    }

    public function getProgress()
    {
        return floor(($this->currentPage/$this->maxPage)*100);
    }

    /**
     * @return string
     */
    public function getResultText()
    {
        return $this->resultText;
    }
}