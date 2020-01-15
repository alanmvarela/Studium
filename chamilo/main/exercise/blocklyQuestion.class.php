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

    // $blockly_url contiene la URL a la version de blockly utilizada por studium.
    public static $blockly_url = 'http://blockly-studium';

    // $blockly_games_url contiene la parte de la url de blockly-games correpondiente a cada juego disponible.
    public static $blockly_games_url = [
      1 => 'puzzle',
      2 => 'maze',
      3 => 'bird',
      4 => 'turtle',
      5 => 'movie',
      6 => 'music',
      7 => 'pond-tutor',
      8 => 'pond',
    ];

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
        $blockly_default_games = self::getBlocklyDefaultGamesList();
        $form->addSelect('blockly_selected_game', get_lang('BlocklySelectedGame'), $blockly_default_games);
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
        $this->extra = $form->getSubmitValue('blockly_selected_game');
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

    /**
     * Devuelve un arreglo con los nombres de los juegos disponibles en
     * Blockly-Games segun el lenguaje de la plataforma.
     */
    public static function getBlocklyDefaultGamesList()
    {
      $select_blockly_game = [];

      foreach (SELF::$blockly_games_url as $index => $value){
        $select_blockly_game[$index] = get_lang(str_replace('-','',$value));
      }

      return $select_blockly_game;
    }

    /**
     * Genera y devuelve la URL al juego de Blockly-Games pasado por parametro y segun el lenguaje de la plataforma.
     *
     * @param int $game_id
     *
     */
    public function getGameURL($game_id)
    {
      return SELF::$blockly_url.sprintf(get_lang('BlocklyUrl'), SELF::$blockly_games_url[$game_id]);
    }

}
