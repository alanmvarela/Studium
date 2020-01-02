<?php
/* For licensing terms, see /license.txt */

/**
 * File containing the BlocklyQuestion class.
 * This class allows to instantiate an object of type BLOCKLY_QUESTION,
 * extending the class question.
 *
 * @package chamilo.exercise
 *
 * @author Alan Varela
 */
class BlocklyQuestion extends Question
{
    public static $typePicture = 'free_answer.png';
    public static $explanationLangVar = 'BlocklyQuestion';

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = BLOCKLY_QUESTION;
        $this->isContent = $this->getIsContent();
    }

    /**
     * {@inheritdoc}
     */
    public function createAnswersForm($form)
    {
        $form->addElement('text', 'weighting', get_lang('Weighting'));
        global $text;
        // setting the save button here and not in the question class.php
        $form->addButtonSave($text, 'submitQuestion');
        if (!empty($this->id)) {
            $form->setDefaults(['weighting' => float_format($this->weighting, 1)]);
        } else {
            if ($this->isContent == 1) {
                $form->setDefaults(['weighting' => '10']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processAnswersCreation($form, $exercise)
    {
        $this->weighting = $form->getSubmitValue('weighting');
        $this->save($exercise);
    }

    /**
     * {@inheritdoc}
     */
    public function return_header($exercise, $counter = null, $score = [])
    {
        $score['revised'] = $this->isQuestionWaitingReview($score);
        $header = parent::return_header($exercise, $counter, $score);
        $header .= '<table class="'.$this->question_table_class.'" >
        <tr>
        <th>'.get_lang('Answer').'</th>
        </tr>';

        return $header;
    }
}
