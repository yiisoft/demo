<?php
declare(strict_types=1);

namespace App\Invoice\Helpers;

use App\Invoice\Setting\SettingRepository as SRepo;
use App\Invoice\Helpers\DateHelper as DHelp;
use Yiisoft\Html\Html;

Class CustomValuesHelper {

    private SRepo $s;
    private DHelp $d;

    public function __construct(SRepo $s) {
        $this->s = $s;
        $this->d = new DHelp($s);
    }

    public function format_date($txt) {
        if ($txt == null) {
            return '';
        }
        return $this->d->date_from_mysql($txt);
    }

    /**
     * @param $txt
     * @return string
     */
    public function format_text($txt) {
        if ($txt == null) {
            return '';
        }
        return $txt;
    }

    /**
     * @param $txt
     * @return string
     */
    public function format_singlechoice($txt) {
        if ($txt == null) {
            return '';
        }
        $el = $this->cvR->repoCustomValuequery($txt);
        return $el->value;
    }

    /**
     * @param $txt
     * @return string
     */
    public function format_multiplechoice($txt) {
        if ($txt == null) {
            return '';
        }
        $explode = explode(',', $txt);
        $values = $this->cvR->repoCustomValueIdInquery($explode);
        $values_text = [];

        foreach ($values as $value) {
            $values_text[] = $value->getValue();
        }

        return implode("\n", $values_text);
    }

    /**
     * @param $txt
     * @return string
     */
    public function format_boolean($txt) {
        if ($txt == null) {
            return '';
        }

        if ($txt == '1') {
            return $this->s->trans('true');
        } else if ($txt == '0') {
            return $this->s->trans('false');
        }

        return '';
    }

    /**
     * @param $txt
     * @return string
     */
    public function format_avs($txt) {
        if (!preg_match('/(\d{3})(\d{4})(\d{4})(\d{2})/', $txt, $matches)) {
            return $txt;
        }
        return $matches[1] . "." . $matches[2] . "." . $matches[3] . "." . $matches[4];
    }

    /**
     * @param $txt
     * @return string
     */
    public function format_fallback($txt) {
        return format_text($txt);
    }

    /**
     * Print a custom form field based on the type
     * @param $encode
     * @param $quote_custom_values
     * @param $custom_field
     * @param $custom_value
     * @param string $class_top
     * @param string $class_surrounding_top
     * @param string $label_class
     */    
     
     // print_field_for_view uses eg. for=view[1] whereas print_field_for_form uses eg. for=custom[1]
     // This is to avoid a conflict between the two on the quote/view
     // quotecontroller/custom_fields function contains the preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches)
     // which uses 'custom'[ not 'view'[ to differentiate between the two.
     // Fields in the view are disabled
     public function print_field_for_view($entity_custom_values, $custom_field, $custom_value, $class_top = '', $class_surrounding_top = 'controls', $label_class = 'label label-primary') {
        ?>
        <div>
            <div class="<?php echo $class_top; ?>">
                <label<?php echo($label_class != '' ? " class='" . $label_class . "'" : ''); ?>
                    for="view[<?php echo $custom_field->getId(); ?>]">
                    <?= "   ".Html::encode($custom_field->getLabel()); ?>
                </label>
            </div>
        <?php
            $fieldValue = $this->form_value($entity_custom_values, $custom_field->getId()) ? $this->form_value($entity_custom_values, $custom_field->getId()) : "" ;
        ?>
            <div class="<?= $class_surrounding_top; ?>">
        <?php
        switch ($custom_field->getType()) {
                case 'DATE':
                    $dateValue = $fieldValue == "" ? "" : $fieldValue;                    
                    ?>
                <input type="text" class="form-control input-sm datepicker" disabled autocomplete="off" role="presentation"
                               name="view[<?= $custom_field->getId(); ?>]"
                               id="<?= $custom_field->getId(); ?>"
                               value="<?= $dateValue; ?>">
                    <?php
                    break;
                case 'SINGLE-CHOICE':
                    $choices = $custom_value[$custom_field->getId()];
                    ?>
                        <select class="form-control" name="view[<?= $custom_field->getId(); ?>]" disabled
                                id="<?= $custom_field->getId(); ?>">
                            <option value=""><?= $this->s->trans('none'); ?></option>
                        <?php foreach ($choices as $single): ?>
                                <option value="<?= $single->getId(); ?>"
                                    <?php $this->s->check_select($single->getId(), $fieldValue); ?>>
                                    <?php Html::encode($single->getValue()); ?>
                                </option>
                               <?php endforeach; ?>
                        </select>
                    <?php
                    break;
                case 'MULTIPLE-CHOICE':
                    $choices = $custom_value[$custom_field->getId()];
                    $selChoices = explode(',', $fieldValue);
                    ?>
                        <select id="<?= $custom_field->getId(); ?>" name="view[<?= $custom_field->getId(); ?>]" class="form-control" disabled>
                            <option value=""><?= $this->s->trans('none'); ?></option>
                            <?php foreach ($choices as $choice): ?>
                                <option value="<?= $choice->getId(); ?>" <?php echo $this->s->check_select(in_array($choice->getId(), $selChoices)); ?>>
                            <?= Html::encode($choice->getValue()); ?>
                                </option>
                        <?php endforeach; ?>
                        </select>

                    <?php
                    break;
                case 'BOOLEAN':
                    ?>
                        <select id="<?= $custom_field->getId(); ?>"
                                name="view[<?= $custom_field->getId(); ?>]"
                                class="form-control" 
                                disabled>
                            <option value="0" <?= $this->s->check_select($fieldValue, '0'); ?>><?= $this->s->trans('false'); ?></option>
                            <option value="1" <?php $this->s->check_select($fieldValue, '1'); ?>><?= $this->s->trans('true'); ?></option>
                        </select>
                        <?php
                        break;
                    default:
                        ?>
                        <input type="text" class="form-control" 
                               name="view[<?= $custom_field->getId(); ?>]"
                               id="<?= $custom_field->getId(); ?>"
                               value="<?= Html::encode($fieldValue); ?>"
                               disabled>
        <?php } ?>
            </div>
        </div>
        <?php
    }
    
    // Note: $custom_value can be an array of dropdown list values
    // eg see payment/_form.php            
    public function print_field_for_form($entity_custom_values, $custom_field, $custom_value, $class_input = '', $class_before_input_class = 'controls', $label_class ='') {
        ?>
        
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label<?php echo($label_class != '' ? " class='" . $label_class . "'" : ''); ?>
                      for="custom[<?= $custom_field->getId(); ?>]">
                    <?= "   ".Html::encode($custom_field->getLabel()); ?>
                    <?= ($custom_field->getType() === 'DATE' ? " (".$this->d->display().")" : ''); ?>
                </label>
            </div>
        <?php
            $fieldValue = $this->form_value($entity_custom_values, $custom_field->getId()) ? $this->form_value($entity_custom_values, $custom_field->getId()) : " " ;
        ?>
            <div class="<?= $class_before_input_class; ?>">
        <?php
        switch ($custom_field->getType()) {
                case 'DATE':
                    $dateValue = $fieldValue == "" ? "" : $fieldValue;                    
                    ?>
                    <div class="<?php echo $class_input; ?>">
                        <input type="text" class="form-control input-sm datepicker" style="position: relative; z-index: 100000;" roles="presentations" autocomplete="off" readonly
                               name="custom[<?= $custom_field->getId(); ?>]"
                               id="<?= $custom_field->getId(); ?>"
                               value="<?= $dateValue; ?>" required>
                    </div>    
                    <?php
                    break;
                case 'SINGLE-CHOICE':
                    $choices = $custom_value[$custom_field->getId()];
                    ?>
                    <div class="<?php echo $class_input; ?>">
                        <select class="form-control" name="custom[<?= $custom_field->getId(); ?>]"
                                id="<?= $custom_field->getId(); ?>" required>
                            <option value=""><?= $this->s->trans('none'); ?></option>
                        <?php foreach ($choices as $single): ?>
                                <option value="<?= $single->getId(); ?>"
                    <?php $this->s->check_select($single->getId(), $fieldValue); ?>>
                    <?php Html::encode($single->getValue()); ?>
                                </option>
                               <?php endforeach; ?>
                        </select>
                    </div>    
                    <?php
                    break;
                case 'MULTIPLE-CHOICE':
                    $choices = $custom_value[$custom_field->getId()];
                    $selChoices = explode(',', $fieldValue);
                    ?>
                    <div class="<?php echo $class_input; ?>">
                        <select id="<?= $custom_field->getId(); ?>" name="custom[<?= $custom_field->getId(); ?>]" class="form-control" required>
                            <option value=""><?= $this->s->trans('none'); ?></option>
                            <?php foreach ($choices as $choice): ?>
                                <option value="<?= $choice->getId(); ?>" <?php echo $this->s->check_select(in_array($choice->getId(), $selChoices)); ?>>
                            <?= Html::encode($choice->getValue()); ?>
                                </option>
                        <?php endforeach; ?>
                        </select>
                    </div>        
                    <?php
                    break;
                case 'BOOLEAN':
                    ?>
                    <div class="<?php echo $class_input; ?>">
                        <select id="<?= $custom_field->getId(); ?>"
                                name="custom[<?= $custom_field->getId(); ?>]"
                                class="form-control" >
                            <option value="0" <?= $this->s->check_select($fieldValue, '0'); ?>><?= $this->s->trans('false'); ?></option>
                            <option value="1" <?php $this->s->check_select($fieldValue, '1'); ?>><?= $this->s->trans('true'); ?></option>
                        </select>
                    </div>
                 <?php
                        break;
                case 'NUMBER':
                     ?>
                    <div class="<?php echo $class_input; ?>">
                        <input type="number" class="form-control" 
                               name="custom[<?= $custom_field->getId(); ?>]"
                               id="<?= $custom_field->getId(); ?>"
                               value="<?= Html::encode($fieldValue); ?>" required>
                    </div>    
                        <?php
                        break;
                default:
                        ?>
                    <div class="<?php echo $class_input; ?>">
                        <input type="text" class="form-control" 
                               name="custom[<?= $custom_field->getId(); ?>]"
                               id="<?= $custom_field->getId(); ?>"
                               value="<?= Html::encode($fieldValue); ?>" required>
                    </div>    
        <?php } ?>
            </div>        
        <?php
    }
    
    public function print_field_for_pdf($entity_custom_values, $custom_field, $cvR) {
        ?>
        <div>
            <div>
                <label>
                    <b><?= "   ".Html::encode($custom_field->getLabel()); ?></b>
                </label>
            </div>
        <?php
            $fieldValue = $this->form_value($entity_custom_values, $custom_field->getId()) ? $this->form_value($entity_custom_values, $custom_field->getId()) : "" ;
        ?>
            <div>
        <?php
        switch ($custom_field->getType()) {
                case 'DATE':
                    $dateValue = $fieldValue == "" ? "" : $fieldValue;                    
                    ?>
                    <label><?= $dateValue; ?></label><br><br>                      
                    <?php
                    break;
                case 'SINGLE-CHOICE':                    
                    ?>
                    <label><?php echo $this->selected_value($entity_custom_values,$custom_field->getId(),$cvR); ?></label><br>    
                    <?php
                    break;
                case 'MULTIPLE-CHOICE':                    
                    ?>
                    <label><?php echo $this->selected_value($entity_custom_values,$custom_field->getId(),$cvR); ?></label><br><br>     
                    <?php
                    break;
                case 'BOOLEAN':
                    ?>
                    <label><?php echo ($this->form_value($entity_custom_values,$custom_field->getId()) ? 'True': 'False'); ?></label><br><br>   
                        <?php
                    break;
                case 'NUMBER':
                        ?>
                    <div><?= Html::encode($fieldValue); ?></div><br><br>     
                    <?php
                    break;
                default:
                        ?>
                    <div><?= Html::encode($fieldValue); ?></div><br><br>     
        <?php } ?>
            </div>
        </div>
        <?php
    }

    public function form_value($entity_custom_values, $custom_field_id) {                                                                                                                                         
        foreach ($entity_custom_values as $entity_custom_value) {
            if ($entity_custom_value->getCustom_field_id() === (string) $custom_field_id) {
                return $entity_custom_value->getValue();
            }
        }
    }
        
    public function selected_value($entity_custom_values, $custom_field_id, $cvR){
        $quote_custom_value = $this->form_value($entity_custom_values,$custom_field_id);
        if (($quote_custom_value !== '') && !empty($quote_custom_value)) {
          $custom_value = $cvR->repoCustomValuequery((string)$quote_custom_value);
          return $selected_value = $custom_value->getValue();
        } 
        return $selected_value = '';
    }
}
