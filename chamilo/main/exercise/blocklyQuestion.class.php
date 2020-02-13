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
    public static $typePicture = 'blockly_question.png';
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
      8 => 'pond-duck',
    ];

    public static $blockly_default_levels = [
      1 => "1",
      2 => "2",
      3 => "3",
      4 => "4",
      5 => "5",
      6 => "6",
      7 => "7",
      8 => "8",
      9 => "9",
      10 => "10",
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

        $form->addHtml('<div id="blockly_level" name="blockly_level" hidden="true" >');
        $form->addSelect('blockly_selected_level', get_lang('BlocklySelectedLevel'), SELF::$blockly_default_levels);
        $form->addHtml('</div>');

        $form->addElement('text', 'weighting', get_lang('Weighting'));
        global $text;
        $form->addButtonSave($text, 'submitQuestion');

        $form->addElement('html',
          '<script>
          $(\'select[name="blockly_selected_game"]\').on(\'change\', function() {
            $selectedGame=this.value;
            if($selectedGame == 1 || $selectedGame == 8 ){
              $(\'#blockly_level\').hide();
              $(\'select[name="blockly_selected_level"]\').val(1);
            }else{
              $(\'#blockly_level\').show();
            }
          });
          </script>'
        );

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
        $this->extra = htmlspecialchars($form->getSubmitValue('blockly_selected_game').
                                        "&".$form->getSubmitValue('blockly_selected_level'));
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
    public function getGameURL($game_type)
    {
      $game_type_data = explode("&",htmlspecialchars_decode($game_type));
      return SELF::$blockly_url.sprintf(get_lang('BlocklyUrl'),
                                        SELF::$blockly_games_url[$game_type_data[0]],
                                        $game_type_data[1]);
    }

}
